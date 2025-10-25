<?php
/**
 * Welcome Page - Redirect to appropriate page
 * ExpenseLogger - خرج‌نگار
 */

require_once __DIR__ . '/config/init.php';

// If password not set, redirect to unlock page for setup
if (!isPasswordSet()) {
    redirect('unlock.php');
    exit;
}

// If app is locked, redirect to unlock page
if (isAppLocked()) {
    redirect('unlock.php');
    exit;
}

// Otherwise, redirect to main application
redirect('index.php');
exit;
