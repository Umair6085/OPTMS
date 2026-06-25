<?php
// controllers/AdminController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Meeting.php';

class AdminController {

    public function manageTeachers() {
        $error = '';
        $success = '';

        // Handle Form Submissions
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_GET['action'];

            if ($action === 'admin_teacher_create') {
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);
                
                if (empty($name) || empty($email) || empty($password)) {
                    $error = 'All fields are required.';
                } elseif (User::findByEmail($email)) {
                    $error = 'Email is already registered.';
                } else {
                    User::create($name, $email, $password, 'Teacher', 'Active');
                    $success = 'Teacher profile created successfully!';
                }
            } elseif ($action === 'admin_teacher_edit') {
                $id = (int)$_POST['user_id'];
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $password = trim($_POST['password']);

                if (empty($name) || empty($email)) {
                    $error = 'Name and Email are required.';
                } else {
                    User::update($id, $name, $email, !empty($password) ? $password : null);
                    $success = 'Teacher profile updated successfully!';
                }
            }
        }

        // Handle Deletions
        if ($_GET['action'] === 'admin_teacher_delete') {
            $id = (int)$_GET['id'];
            User::delete($id);
            $success = 'Teacher profile deleted.';
        }

        // Fetch teachers list
        $teachers = User::getByRole('Teacher');
        include __DIR__ . '/../views/admin/teachers.php';
    }

    public function verifyParents() {
        $success = '';
        
        if (isset($_GET['id']) && isset($_GET['action'])) {
            $id = (int)$_GET['id'];
            if ($_GET['action'] === 'admin_parent_approve') {
                User::updateStatus($id, 'Active');
                $success = 'Parent verified and account activated successfully!';
            } elseif ($_GET['action'] === 'admin_parent_reject') {
                User::delete($id); // CASCADE deletes matching Student
                $success = 'Parent registration rejected and deleted.';
            }
        }

        $pendingParents = User::getPendingParents();
        include __DIR__ . '/../views/admin/parents.php';
    }

    public function ptmEvents() {
        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'admin_event_create') {
            $date = $_POST['event_date'];
            $start = $_POST['start_time'];
            $end = $_POST['end_time'];
            $duration = (int)$_POST['slot_duration'];

            if (empty($date) || empty($start) || empty($end)) {
                $error = 'All fields are required.';
            } else {
                Meeting::createPtmEvent($date, $start, $end, $duration);
                
                // Auto generate time slots for all active teachers
                require_once __DIR__ . '/../models/TimeSlots.php';
                $count = TimeSlots::generateFromEvent($date, $start, $end, $duration);
                
                $success = "PTM Event created successfully! Generated $count available teacher slots.";
            }
        }

        if ($_GET['action'] === 'admin_event_delete') {
            $id = (int)$_GET['id'];
            Meeting::deletePtmEvent($id);
            $success = 'PTM Event deleted.';
        }

        $events = Meeting::getPtmEvents();
        include __DIR__ . '/../views/admin/events.php';
    }

    public function toggleSettings() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            require_once __DIR__ . '/../models/Settings.php';
            $restrict = isset($_POST['restrict_dues_booking']) ? '1' : '0';
            Settings::set('restrict_dues_booking', $restrict);
            $_SESSION['settings_success'] = 'Dues restriction settings updated successfully!';
        }
        redirect('index.php?action=dashboard');
    }

    public function manageSlots() {
        $error = '';
        $success = '';
        require_once __DIR__ . '/../models/TimeSlots.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_GET['action'];
            if ($action === 'admin_slot_create') {
                $teacherId = (int)$_POST['teacher_id'];
                $slotTime = $_POST['slot_time'];
                $duration = (int)$_POST['duration'];
                
                if (empty($teacherId) || empty($slotTime) || empty($duration)) {
                    $error = 'All fields are required.';
                } else {
                    TimeSlots::create($teacherId, $slotTime, $duration, 'Available');
                    $success = 'Custom time slot added successfully!';
                }
            } elseif ($action === 'admin_slot_edit') {
                $slotId = (int)$_POST['slot_id'];
                $teacherId = (int)$_POST['teacher_id'];
                $slotTime = $_POST['slot_time'];
                $duration = (int)$_POST['duration'];
                $status = $_POST['status'];

                if (empty($teacherId) || empty($slotTime) || empty($duration) || empty($status)) {
                    $error = 'All fields are required.';
                } else {
                    TimeSlots::update($slotId, $teacherId, $slotTime, $duration, $status);
                    $success = 'Time slot details updated successfully!';
                }
            }
        }

        if ($_GET['action'] === 'admin_slot_delete') {
            $slotId = (int)$_GET['id'];
            TimeSlots::delete($slotId);
            $success = 'Time slot deleted successfully.';
        }

        $teachers = User::getByRole('Teacher');
        $slots = TimeSlots::getAllSlots();
        
        // Also fetch booked meetings to reassign teachers
        global $pdo;
        $stmt = $pdo->prepare("SELECT m.*, u_parent.name AS parent_name, u_teacher.name AS teacher_name 
                               FROM Meetings m
                               JOIN Users u_parent ON m.parent_id = u_parent.user_id
                               JOIN Users u_teacher ON m.teacher_id = u_teacher.user_id
                               ORDER BY m.slot_time DESC");
        $stmt->execute();
        $bookedMeetings = $stmt->fetchAll();

        include __DIR__ . '/../views/admin/slots.php';
    }

    public function assignTeacher() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $meetingId = (int)$_POST['meeting_id'];
            $newTeacherId = (int)$_POST['teacher_id'];
            
            global $pdo;
            $stmt = $pdo->prepare("UPDATE Meetings SET teacher_id = ? WHERE meeting_id = ?");
            $stmt->execute([$newTeacherId, $meetingId]);
            
            $_SESSION['assign_success'] = 'Teacher successfully reassigned to meeting!';
        }
        redirect('index.php?action=admin_slots');
    }

    public function manageExpenses() {
        $error = '';
        $success = '';
        require_once __DIR__ . '/../models/Expenses.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $action = $_GET['action'];
            if ($action === 'admin_expense_create') {
                $title = trim($_POST['title']);
                $amount = (float)$_POST['amount'];
                $category = trim($_POST['category']);
                $date = $_POST['expense_date'];
                $desc = trim($_POST['description'] ?? '');

                if (empty($title) || empty($amount) || empty($date)) {
                    $error = 'Title, Amount, and Date are required.';
                } else {
                    Expenses::create($title, $amount, $category, $date, $desc);
                    $success = 'Expense record added successfully!';
                }
            } elseif ($action === 'admin_expense_edit') {
                $id = (int)$_POST['expense_id'];
                $title = trim($_POST['title']);
                $amount = (float)$_POST['amount'];
                $category = trim($_POST['category']);
                $date = $_POST['expense_date'];
                $desc = trim($_POST['description'] ?? '');

                if (empty($title) || empty($amount) || empty($date)) {
                    $error = 'Title, Amount, and Date are required.';
                } else {
                    Expenses::update($id, $title, $amount, $category, $date, $desc);
                    $success = 'Expense record updated successfully!';
                }
            }
        }

        if ($_GET['action'] === 'admin_expense_delete') {
            $id = (int)$_GET['id'];
            Expenses::delete($id);
            $success = 'Expense record deleted.';
        }

        $expenses = Expenses::getAll();
        $totalExpenses = Expenses::getTotalExpenses();
        include __DIR__ . '/../views/admin/expenses.php';
    }

    public function viewFeedback() {
        global $pdo;
        // Fetch all parent feedback reviews
        $stmt = $pdo->prepare("SELECT f.*, m.slot_time, u_parent.name AS parent_name, u_teacher.name AS teacher_name
                               FROM Feedback f
                               JOIN Meetings m ON f.meeting_id = m.meeting_id
                               JOIN Users u_parent ON m.parent_id = u_parent.user_id
                               JOIN Users u_teacher ON m.teacher_id = u_teacher.user_id
                               ORDER BY f.created_at DESC");
        $stmt->execute();
        $feedbacks = $stmt->fetchAll();

        include __DIR__ . '/../views/admin/feedback.php';
    }

    public function financialReports() {
        $payments = Payment::getAllPayments();
        $summary = Payment::getFinancialSummary();

        // Check if export action
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            
            if ($action === 'admin_reports_export_csv') {
                // Export CSV
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment; filename="OPTMS_Financial_Report_' . date('Y-m-d') . '.csv"');
                
                $output = fopen('php://output', 'w');
                fputcsv($output, ['Payment ID', 'Student Name', 'Reg No', 'Parent Name', 'Amount (PKR)', 'JazzCash Trx ID', 'Payment Date', 'Status']);
                
                foreach ($payments as $payment) {
                    fputcsv($output, [
                        $payment['payment_id'],
                        $payment['student_name'],
                        $payment['registration_no'],
                        $payment['parent_name'],
                        $payment['amount'],
                        $payment['jazzcash_trx_id'] ?: 'N/A',
                        $payment['payment_date'] ?: 'N/A',
                        $payment['status']
                    ]);
                }
                
                // Add Summary totals in CSV
                fputcsv($output, []);
                fputcsv($output, ['Total Revenue', 'Actual Expenses', 'Net Profit']);
                fputcsv($output, [$summary['total_earnings'], $summary['simulated_expenses'], $summary['net_profit']]);
                fclose($output);
                exit();
            } elseif ($action === 'admin_reports_export_pdf') {
                // PDF Print View rendering
                include __DIR__ . '/../views/admin/reports_pdf.php';
                exit();
            }
        }

        include __DIR__ . '/../views/admin/reports.php';
    }
}
?>
