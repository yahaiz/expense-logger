<?php
/**
 * Logout Script - Disabled for Single-User Mode
 * ExpenseLogger - خرج‌نگار
 */

require_once __DIR__ . '/config/init.php';

// This application is now configured for single-user offline operation
// Logout is not applicable
setFlashMessage('info', 'This application is configured for single-user offline operation. Logout is not applicable.');
redirect('index.php');
exit;