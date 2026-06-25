<?php
// index.php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Load all models
require_once __DIR__ . '/models/User.php';
require_once __DIR__ . '/models/Student.php';
require_once __DIR__ . '/models/Payment.php';
require_once __DIR__ . '/models/Dmc.php';
require_once __DIR__ . '/models/Meeting.php';
require_once __DIR__ . '/models/Feedback.php';
require_once __DIR__ . '/models/Settings.php';
require_once __DIR__ . '/models/TimeSlots.php';
require_once __DIR__ . '/models/Expenses.php';
require_once __DIR__ . '/models/AcademicRecords.php';

// Load all controllers
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/controllers/AdminController.php';
require_once __DIR__ . '/controllers/TeacherController.php';
require_once __DIR__ . '/controllers/ParentController.php';

$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';

// Public Actions (No Auth Required)
if ($action === 'login') {
    $auth = new AuthController();
    $auth->login();
    exit();
} elseif ($action === 'register') {
    $auth = new AuthController();
    $auth->register();
    exit();
}

// Authentication Gatekeeper
if (!isset($_SESSION['user_id'])) {
    redirect('index.php?action=login');
}

// Log Out Action
if ($action === 'logout') {
    $auth = new AuthController();
    $auth->logout();
    exit();
}

// Dynamic Action Routing
switch ($action) {
    case 'dashboard':
        $auth = new AuthController();
        $auth->dashboard();
        break;

    // ADMIN ACTIONS
    case 'admin_teachers':
    case 'admin_teacher_create':
    case 'admin_teacher_edit':
    case 'admin_teacher_delete':
        check_role('Admin');
        $admin = new AdminController();
        $admin->manageTeachers();
        break;

    case 'admin_settings_toggle':
        check_role('Admin');
        $admin = new AdminController();
        $admin->toggleSettings();
        break;

    case 'admin_slots':
    case 'admin_slot_create':
    case 'admin_slot_edit':
    case 'admin_slot_delete':
        check_role('Admin');
        $admin = new AdminController();
        $admin->manageSlots();
        break;

    case 'admin_assign_teacher':
        check_role('Admin');
        $admin = new AdminController();
        $admin->assignTeacher();
        break;

    case 'admin_parents':
    case 'admin_parent_approve':
    case 'admin_parent_reject':
        check_role('Admin');
        $admin = new AdminController();
        $admin->verifyParents();
        break;

    case 'admin_events':
    case 'admin_event_create':
    case 'admin_event_delete':
        check_role('Admin');
        $admin = new AdminController();
        $admin->ptmEvents();
        break;

    case 'admin_reports':
    case 'admin_reports_export_csv':
    case 'admin_reports_export_pdf':
        check_role('Admin');
        $admin = new AdminController();
        $admin->financialReports();
        break;

    case 'admin_expenses':
    case 'admin_expense_create':
    case 'admin_expense_edit':
    case 'admin_expense_delete':
        check_role('Admin');
        $admin = new AdminController();
        $admin->manageExpenses();
        break;

    case 'admin_feedback':
        check_role('Admin');
        $admin = new AdminController();
        $admin->viewFeedback();
        break;

    // TEACHER ACTIONS
    case 'teacher_profile':
    case 'teacher_profile_update':
        check_role('Teacher');
        $teacher = new TeacherController();
        $teacher->manageProfile();
        break;

    case 'teacher_academic_records':
    case 'teacher_record_save':
    case 'teacher_record_delete':
        check_role('Teacher');
        $teacher = new TeacherController();
        $teacher->manageAcademicRecords();
        break;

    case 'teacher_dmcs':
    case 'teacher_dmc_save':
        check_role('Teacher');
        $teacher = new TeacherController();
        $teacher->manageDMCs();
        break;

    case 'teacher_conduct_meeting':
        check_role('Teacher');
        $teacher = new TeacherController();
        $teacher->conductMeeting();
        break;

    case 'teacher_feedback':
    case 'teacher_feedback_forward':
        check_role('Teacher');
        $teacher = new TeacherController();
        $teacher->viewFeedback();
        break;

    // PARENT ACTIONS
    case 'parent_register_student':
        check_role('Parent');
        $parent = new ParentController();
        $parent->registerStudent();
        break;

    case 'parent_dues':
    case 'parent_pay':
        check_role('Parent');
        $parent = new ParentController();
        $parent->verifyDues();
        break;

    case 'parent_book_slot':
        check_role('Parent');
        $parent = new ParentController();
        $parent->bookSlot();
        break;

    case 'parent_meeting_room':
        check_role('Parent');
        $parent = new ParentController();
        $parent->meetingRoom();
        break;

    case 'parent_submit_feedback':
        check_role('Parent');
        $parent = new ParentController();
        $parent->submitFeedback();
        break;

    // JAZZCASH SANDBOX (Parent Payment Mock Gateway)
    case 'jazzcash_sandbox':
        check_role('Parent');
        $parent = new ParentController();
        $parent->jazzcashSandbox();
        break;

    default:
        // Page Not Found or Fallback
        header("HTTP/1.0 404 Not Found");
        include __DIR__ . '/views/error.php';
        break;
}
?>
