<?php
// controllers/TeacherController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Student.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Dmc.php';
require_once __DIR__ . '/../models/Meeting.php';
require_once __DIR__ . '/../models/Feedback.php';

class TeacherController {

    public function manageDMCs() {
        $error = '';
        $success = '';

        // Handle DMC Saves
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'teacher_dmc_save') {
            $studentId = (int)$_POST['student_id'];
            $totalMarks = (int)$_POST['total_marks'];
            $obtainedMarks = (int)$_POST['obtained_marks'];
            $teacherId = $_SESSION['user_id'];
            
            $pdfPath = null;

            // Handle PDF File Upload
            if (isset($_FILES['dmc_pdf']) && $_FILES['dmc_pdf']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['dmc_pdf']['tmp_name'];
                $fileName = $_FILES['dmc_pdf']['name'];
                $fileSize = $_FILES['dmc_pdf']['size'];
                $fileType = $_FILES['dmc_pdf']['type'];
                
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                if ($fileExtension === 'pdf') {
                    $newFileName = 'dmc_' . $studentId . '_' . time() . '.pdf';
                    $uploadFileDir = __DIR__ . '/../uploads/dmcs/';
                    
                    // Create directory if not exists
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }

                    $dest_path = $uploadFileDir . $newFileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $pdfPath = 'uploads/dmcs/' . $newFileName;
                    } else {
                        $error = 'Error moving uploaded DMC file.';
                    }
                } else {
                    $error = 'Only PDF file uploads are supported for DMCs.';
                }
            }

            if (empty($error)) {
                try {
                    Dmc::save($studentId, $teacherId, $totalMarks, $obtainedMarks, $pdfPath);
                    $success = 'Student DMC academic record saved successfully!';
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        // Fetch list of all approved active students in system to write records for
        global $pdo;
        $stmt = $pdo->prepare("SELECT s.*, u.name AS parent_name, d.obtained_marks, d.total_marks, d.pdf_file_path 
                               FROM Students s
                               JOIN Users u ON s.parent_id = u.user_id
                               LEFT JOIN DMCs d ON d.student_id = s.student_id
                               WHERE u.status = 'Active'
                               ORDER BY s.full_name ASC");
        $stmt->execute();
        $studentsList = $stmt->fetchAll();

        include __DIR__ . '/../views/teacher/dmcs.php';
    }

    public function conductMeeting() {
        $meetingId = (int)$_GET['id'];
        $meeting = Meeting::findById($meetingId);

        if (!$meeting || $meeting['teacher_id'] != $_SESSION['user_id']) {
            redirect('index.php?action=dashboard');
        }

        // Fetch parent details
        $parent = User::findById($meeting['parent_id']);
        
        // Fetch student linked to parent
        $student = null;
        $dmc = null;
        $academicRecords = [];
        if ($parent) {
            $students = Student::getByParent($parent['user_id']);
            if (!empty($students)) {
                $student = $students[0]; // Active student
                $dmcList = Dmc::getByStudent($student['student_id']);
                if (!empty($dmcList)) {
                    $dmc = $dmcList[0];
                }
                require_once __DIR__ . '/../models/AcademicRecords.php';
                $academicRecords = AcademicRecords::getByStudent($student['student_id']);
            }
        }

        // Update meeting status to Completed when teacher joins or marks it.
        // For simulation, we keep scheduled but add a trigger to mark as completed.
        if (isset($_GET['status_update'])) {
            Meeting::updateStatus($meetingId, $_GET['status_update']);
            redirect('index.php?action=dashboard');
        }

        include __DIR__ . '/../views/teacher/meeting_room.php';
    }

    public function viewFeedback() {
        $success = '';
        if (isset($_GET['action']) && $_GET['action'] === 'teacher_feedback_forward') {
            $feedbackId = (int)$_GET['id'];
            Feedback::flagForAdmin($feedbackId);
            $success = 'Critical issues forwarded directly to system Admin.';
        }

        $feedbacks = Feedback::getByTeacher($_SESSION['user_id']);
        include __DIR__ . '/../views/teacher/feedback.php';
    }

    public function manageProfile() {
        $error = '';
        $success = '';
        $user = User::findById($_SESSION['user_id']);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'teacher_profile_update') {
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $password = trim($_POST['password'] ?? '');

            if (empty($name) || empty($email)) {
                $error = 'Name and email are required.';
            } else {
                $existing = User::findByEmail($email);
                if ($existing && $existing['user_id'] != $_SESSION['user_id']) {
                    $error = 'Email is already registered by another user.';
                } else {
                    User::update($_SESSION['user_id'], $name, $email, !empty($password) ? $password : null);
                    $_SESSION['name'] = $name;
                    $_SESSION['email'] = $email;
                    $success = 'Profile updated successfully!';
                    $user = User::findById($_SESSION['user_id']);
                }
            }
        }
        include __DIR__ . '/../views/teacher/profile.php';
    }

    public function manageAcademicRecords() {
        $error = '';
        $success = '';
        require_once __DIR__ . '/../models/AcademicRecords.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_GET['action'] === 'teacher_record_save') {
            $recordId = isset($_POST['record_id']) && !empty($_POST['record_id']) ? (int)$_POST['record_id'] : null;
            $studentId = (int)$_POST['student_id'];
            $recordType = $_POST['record_type'];
            $title = trim($_POST['title']);
            $description = trim($_POST['description'] ?? '');
            
            // total_marks and obtained_marks can be empty for behaviour / custom docs
            $totalMarks = !empty($_POST['total_marks']) ? (int)$_POST['total_marks'] : null;
            $obtainedMarks = !empty($_POST['obtained_marks']) ? (int)$_POST['obtained_marks'] : null;
            
            $teacherId = $_SESSION['user_id'];
            $filePath = null;

            // Handle file upload (marked paper or other documents)
            if (isset($_FILES['record_file']) && $_FILES['record_file']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['record_file']['tmp_name'];
                $fileName = $_FILES['record_file']['name'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));
                
                $allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png'];
                if (in_array($fileExtension, $allowedExtensions)) {
                    $newFileName = 'record_' . $studentId . '_' . time() . '.' . $fileExtension;
                    $uploadFileDir = __DIR__ . '/../uploads/records/';
                    if (!is_dir($uploadFileDir)) {
                        mkdir($uploadFileDir, 0777, true);
                    }
                    $dest_path = $uploadFileDir . $newFileName;
                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $filePath = 'uploads/records/' . $newFileName;
                    } else {
                        $error = 'Error saving uploaded record file.';
                    }
                } else {
                    $error = 'Only PDF, JPG, JPEG, and PNG files are supported.';
                }
            }

            if (empty($error)) {
                try {
                    if ($recordId) {
                        AcademicRecords::update($recordId, $studentId, $recordType, $title, $description, $totalMarks, $obtainedMarks, $filePath);
                        $success = 'Academic progress record updated successfully!';
                    } else {
                        AcademicRecords::create($studentId, $teacherId, $recordType, $title, $description, $totalMarks, $obtainedMarks, $filePath);
                        $success = 'Academic progress record created successfully!';
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        }

        if (isset($_GET['action']) && $_GET['action'] === 'teacher_record_delete') {
            $recordId = (int)$_GET['id'];
            AcademicRecords::delete($recordId);
            $success = 'Progress record deleted successfully.';
        }

        // Fetch students list
        global $pdo;
        $stmt = $pdo->prepare("SELECT s.*, u.name AS parent_name 
                               FROM Students s
                               JOIN Users u ON s.parent_id = u.user_id
                               WHERE u.status = 'Active'
                               ORDER BY s.full_name ASC");
        $stmt->execute();
        $studentsList = $stmt->fetchAll();

        // Fetch all academic records by this teacher
        $records = [];
        $stmt = $pdo->prepare("SELECT ar.*, s.full_name AS student_name, s.registration_no 
                               FROM AcademicRecords ar
                               JOIN Students s ON ar.student_id = s.student_id
                               WHERE ar.teacher_id = ?
                               ORDER BY ar.created_at DESC");
        $stmt->execute([$_SESSION['user_id']]);
        $records = $stmt->fetchAll();

        include __DIR__ . '/../views/teacher/academic_records.php';
    }
}
?>
