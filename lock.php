<?php
/**
 * Lock Application
 * ExpenseLogger - خرج‌نگار
 */

require_once __DIR__ . '/config/init.php';

// Lock the application
if (lockApp()) {
    setFlashMessage('info', 'Application has been locked.');
} else {
    setFlashMessage('error', 'Failed to lock application.');
}

// Redirect to unlock page
redirect('unlock.php');
?>