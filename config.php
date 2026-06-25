<?php
// config.php

// Ensure session starts on every page access
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

define('BASE_URL', 'http://localhost/OPTMS');
define('USE_MOCK_APIS', true); // Toggle to simulate JazzCash and Google Meet APIs on localhost

// JazzCash Sandbox Config (Mock or Real)
define('JAZZCASH_MERCHANT_ID', 'MC_MOCK_123');
define('JAZZCASH_PASSWORD', 'MOCK_PASS');
define('JAZZCASH_INTEGERITY_SALT', 'MOCK_SALT');

// Google Calendar API Config (Mock or Real)
define('GOOGLE_CLIENT_ID', 'MOCK_GOOGLE_ID');
define('GOOGLE_CLIENT_SECRET', 'MOCK_GOOGLE_SECRET');

// Helper to construct URLs
function url($path = '') {
    return BASE_URL . '/' . ltrim($path, '/');
}

// Redirect helper
function redirect($path) {
    header("Location: " . url($path));
    exit();
}

// Check authorization roles
function check_role($roles) {
    if (!isset($_SESSION['user_id'])) {
        redirect('index.php?action=login');
    }
    if (is_array($roles)) {
        if (!in_array($_SESSION['role'], $roles)) {
            redirect('index.php?action=dashboard');
        }
    } else {
        if ($_SESSION['role'] !== $roles) {
            redirect('index.php?action=dashboard');
        }
    }
}
?>
