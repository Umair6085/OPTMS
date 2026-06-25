<?php
// controllers/AuthController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Meeting.php';

class AuthController {
    
    public function login() {
        if (isset($_SESSION['user_id'])) {
            redirect('index.php?action=dashboard');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);

            if (empty($email) || empty($password)) {
                $error = 'All fields are required.';
            } else {
                $user = User::findByEmail($email);
                if ($user && password_verify($password, $user['password'])) {
                    if ($user['status'] === 'Pending') {
                        $error = 'Your registration is pending Admin verification.';
                    } else {
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['name'] = $user['name'];
                        $_SESSION['email'] = $user['email'];
                        $_SESSION['role'] = $user['role'];
                        
                        redirect('index.php?action=dashboard');
                    }
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        }
        
        include __DIR__ . '/../views/auth/login.php';
    }

    public function register() {
        if (isset($_SESSION['user_id'])) {
            redirect('index.php?action=dashboard');
        }

        $error = '';
        $success = '';
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password']);
            $confirmPassword = trim($_POST['confirm_password']);
            $role = $_POST['role']; // Parent, Teacher, or Admin (in a real setup, Teacher/Admin are created by existing admins, but for student registration we'll let them register parent and immediately specify student details)
            
            // Parent specific student fields
            $studentName = trim($_POST['student_name'] ?? '');
            $regNo = trim($_POST['registration_no'] ?? '');
            $className = trim($_POST['class_name'] ?? '');
            $dob = trim($_POST['dob'] ?? '');

            if (empty($name) || empty($email) || empty($password)) {
                $error = 'Main fields are required.';
            } elseif ($password !== $confirmPassword) {
                $error = 'Passwords do not match.';
            } elseif (User::findByEmail($email)) {
                $error = 'Email is already registered.';
            } else {
                try {
                    // Start transaction
                    global $pdo;
                    $pdo->beginTransaction();

                    // If role is Parent, we mark user status as 'Pending' until Admin approves them based on student details
                    $status = ($role === 'Parent') ? 'Pending' : 'Active';
                    
                    $userId = User::create($name, $email, $password, $role, $status);

                    if ($role === 'Parent') {
                        if (empty($studentName) || empty($regNo) || empty($className) || empty($dob)) {
                            throw new Exception('Parent registration requires student details.');
                        }
                        
                        // Check if student registration number already exists
                        if (Student::findByRegistrationNo($regNo)) {
                            throw new Exception('Student Registration Number already exists.');
                        }

                        // Create Student linked to Parent
                        $studentId = Student::create($userId, $studentName, $regNo, $className, $dob);

                        // Create an unpaid payment invoice (dues) of Rs. 5000 automatically
                        Payment::createInvoice($studentId, 5000.00);
                    }

                    $pdo->commit();
                    
                    if ($role === 'Parent') {
                        $success = 'Registration submitted! Please wait for Admin approval of student details.';
                    } else {
                        $success = 'Account created successfully! You can now log in.';
                    }
                } catch (Exception $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $error = $e->getMessage();
                }
            }
        }

        include __DIR__ . '/../views/auth/register.php';
    }

    public function logout() {
        session_destroy();
        redirect('index.php?action=login');
    }

    public function dashboard() {
        $role = $_SESSION['role'];
        
        if ($role === 'Admin') {
            // Fetch stats
            global $pdo;
            $teachersCount = count(User::getByRole('Teacher'));
            $pendingParents = count(User::getPendingParents());
            
            // Total meetings scheduled/completed
            $stmt = $pdo->query("SELECT COUNT(*) FROM Meetings");
            $meetingsCount = $stmt->fetchColumn();

            // Total Earnings
            $summary = Payment::getFinancialSummary();
            $earnings = $summary['total_earnings'];

            include __DIR__ . '/../views/admin/dashboard.php';
        } elseif ($role === 'Teacher') {
            $meetings = Meeting::getByTeacher($_SESSION['user_id']);
            include __DIR__ . '/../views/teacher/dashboard.php';
        } elseif ($role === 'Parent') {
            $students = Student::getByParent($_SESSION['user_id']);
            $meetings = Meeting::getByParent($_SESSION['user_id']);
            $hasUnpaid = Payment::hasUnpaidDues($_SESSION['user_id']);
            $dues = Payment::getByParent($_SESSION['user_id']);
            
            include __DIR__ . '/../views/parent/dashboard.php';
        }
    }
}
?>
