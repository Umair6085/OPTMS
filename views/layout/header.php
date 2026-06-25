<?php
// views/layout/header.php
require_once __DIR__ . '/../../config.php';
$action = isset($_GET['action']) ? $_GET['action'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OPTMS - Online Parent-Teacher Meeting System</title>
    <!-- Tailwind CSS for structural grid utilities (optional but recommended for rapid layout structure) -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <!-- Main Premium Custom CSS -->
    <link rel="stylesheet" href="<?php echo url('assets/css/style.css'); ?>">
    <!-- FontAwesome Icons for Premium Interface Symbols -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<?php if (isset($_SESSION['user_id'])): ?>
    <div class="dashboard-container">
        <div class="sidebar-overlay" id="sidebar-overlay"></div>
        <!-- Sidebar Navigation -->
        <aside class="sidebar">
            <div class="sidebar-brand flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-graduation-cap text-indigo-500"></i>
                    <span>OPTMS</span>
                </div>
                <button id="sidebar-close" class="btn-close-sidebar" title="Close Menu">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            
            <ul class="sidebar-menu">
                <li class="sidebar-item <?php echo ($action == 'dashboard') ? 'active' : ''; ?>">
                    <a href="<?php echo url('index.php?action=dashboard'); ?>">
                        <i class="fa-solid fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                
                <?php if ($_SESSION['role'] === 'Admin'): ?>
                    <li class="sidebar-item <?php echo ($action == 'admin_teachers') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=admin_teachers'); ?>">
                            <i class="fa-solid fa-chalkboard-user"></i>
                            <span>Manage Teachers</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo ($action == 'admin_parents') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=admin_parents'); ?>">
                            <i class="fa-solid fa-user-check"></i>
                            <span>Verify Parents</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo ($action == 'admin_events') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=admin_events'); ?>">
                            <i class="fa-solid fa-calendar-days"></i>
                            <span>PTM Scheduling</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo (strpos($action, 'admin_slot') !== false) ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=admin_slots'); ?>">
                            <i class="fa-solid fa-clock"></i>
                            <span>Manage Time Slots</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo (strpos($action, 'admin_expense') !== false) ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=admin_expenses'); ?>">
                            <i class="fa-solid fa-receipt"></i>
                            <span>Expense Reports</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo ($action == 'admin_reports') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=admin_reports'); ?>">
                            <i class="fa-solid fa-file-invoice-dollar"></i>
                            <span>Profit/Loss Reports</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo ($action == 'admin_feedback') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=admin_feedback'); ?>">
                            <i class="fa-solid fa-comments"></i>
                            <span>Parent Feedback</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'Teacher'): ?>
                    <li class="sidebar-item <?php echo (strpos($action, 'teacher_record') !== false || $action === 'teacher_academic_records') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=teacher_academic_records'); ?>">
                            <i class="fa-solid fa-square-poll-vertical"></i>
                            <span>Progress Records</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo (strpos($action, 'teacher_dmc') !== false) ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=teacher_dmcs'); ?>">
                            <i class="fa-solid fa-file-signature"></i>
                            <span>Academic DMCs</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo ($action == 'teacher_feedback') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=teacher_feedback'); ?>">
                            <i class="fa-solid fa-comments"></i>
                            <span>Parent Feedback</span>
                        </a>
                    </li>
                <?php endif; ?>

                <?php if ($_SESSION['role'] === 'Parent'): ?>
                    <li class="sidebar-item <?php echo ($action == 'parent_register_student') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=parent_register_student'); ?>">
                            <i class="fa-solid fa-child-reaching"></i>
                            <span>Register Student</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo ($action == 'parent_dues') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=parent_dues'); ?>">
                            <i class="fa-solid fa-credit-card"></i>
                            <span>Verify & Pay Dues</span>
                        </a>
                    </li>
                    <li class="sidebar-item <?php echo ($action == 'parent_book_slot') ? 'active' : ''; ?>">
                        <a href="<?php echo url('index.php?action=parent_book_slot'); ?>">
                            <i class="fa-solid fa-calendar-check"></i>
                            <span>Book PTM Slot</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <div class="sidebar-footer">
                <a href="<?php echo url('index.php?action=logout'); ?>" class="btn btn-secondary btn-sm btn-block text-center flex items-center justify-center gap-2">
                    <i class="fa-solid fa-arrow-right-from-bracket"></i>
                    <span>Log Out</span>
                </a>
            </div>
        </aside>

        <!-- Main Dashboard View Panel Wrapper -->
        <main class="main-content">
            <div class="top-navbar flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <button id="sidebar-toggle" class="btn-toggle-sidebar" title="Toggle Navigation Menu">
                        <i class="fa-solid fa-bars"></i>
                    </button>
                    <div class="welcome-text">
                        <h2><?php echo htmlspecialchars($_SESSION['name']); ?></h2>
                        <p>Logged in as <strong class="text-indigo-400"><?php echo $_SESSION['role']; ?></strong></p>
                    </div>
                </div>
                <div class="user-profile flex items-center gap-3">
                    <button id="dark-mode-toggle" class="btn-toggle-dark" title="Toggle Light/Dark Theme">
                        <i class="fa-solid fa-moon"></i>
                    </button>
                    <?php if ($_SESSION['role'] === 'Teacher'): ?>
                        <a href="<?php echo url('index.php?action=teacher_profile'); ?>" class="navbar-profile-link <?php echo ($action == 'teacher_profile') ? 'active' : ''; ?>">
                            <i class="fa-solid fa-user-gear"></i>
                            <span>My Profile</span>
                        </a>
                    <?php endif; ?>
                    <span class="text-sm font-semibold hidden md:inline text-slate-350"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($_SESSION['name'], 0, 1)); ?>
                    </div>
                </div>
            </div>
<?php endif; ?>
