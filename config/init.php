<?php
/**
 * Application Initialization
 * ExpenseLogger - خرج‌نگار
 */

// Set timezone
date_default_timezone_set('UTC');

// Error reporting for development
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize session defaults
if (!isset($_SESSION['theme'])) {
    $_SESSION['theme'] = 'light';
}
if (!isset($_SESSION['currency'])) {
    $_SESSION['currency'] = 'USD';
}
if (!isset($_SESSION['calendar'])) {
    $_SESSION['calendar'] = 'gregorian';
}

// Include database configuration
require_once __DIR__ . '/database.php';

// Include Jalali date helper
require_once __DIR__ . '/../includes/jdate.php';

// Setup logging with Monolog
require_once __DIR__ . '/../vendor/autoload.php';
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Processor\UidProcessor;

// Create logger
$logger = new Logger('expenselogger');
$logger->pushProcessor(new UidProcessor());

// Add rotating file handler (keeps 30 days of logs)
$logPath = __DIR__ . '/../logs/app.log';
$rotatingHandler = new RotatingFileHandler($logPath, 30, Logger::INFO);
$logger->pushHandler($rotatingHandler);

// Add error handler for errors and above
$errorHandler = new StreamHandler(__DIR__ . '/../logs/error.log', Logger::ERROR);
$logger->pushHandler($errorHandler);

// Make logger globally available
$GLOBALS['logger'] = $logger;

// Helper function for logging
function logInfo($message, $context = []) {
    global $logger;
    $logger->info($message, $context);
}

function logError($message, $context = []) {
    global $logger;
    $logger->error($message, $context);
}

function logWarning($message, $context = []) {
    global $logger;
    $logger->warning($message, $context);
}

// Helper function to sanitize output (prevent XSS)
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Helper function to get flash messages
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}

// Helper function to set flash messages
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

// Helper function to redirect
function redirect($url) {
    header("Location: $url");
    exit;
}

// Helper function to validate and sanitize input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Helper function to format currency
// Always wrap the currency sign/label in a span.currency-sign so pages
// can style it (for example: make the sign smaller on the dashboard).
function formatCurrency($amount) {
    $currency = $_SESSION['currency'] ?? 'USD';
    if ($currency === 'تومان') {
        // Toman shown after the number
        return number_format($amount, 0) . ' <span class="currency-sign currency-toman">ت</span>';
    } elseif ($currency === 'هزار تومان') {
        return number_format($amount / 1000, 0) . ' <span class="currency-sign currency-toman">هزار ت</span>';
    } else {
        // USD: 100000 تومان = 1 USD, show $ before number
        return '<span class="currency-sign currency-usd">$</span>' . number_format($amount / 100000, 2);
    }
}

// Helper function to format date based on calendar preference
function formatDate($date, $format = 'd M Y') {
    $calendar = $_SESSION['calendar'] ?? 'gregorian';
    
    // Parse the date string safely
    if (is_string($date)) {
        // Assume format YYYY-MM-DD
        $parts = explode('-', $date);
        if (count($parts) === 3) {
            $year = (int)$parts[0];
            $month = (int)$parts[1];
            $day = (int)$parts[2];
        } else {
            // Fallback to current time
            $year = (int)date('Y');
            $month = (int)date('m');
            $day = (int)date('d');
        }
    } else {
        // If timestamp provided
        $year = (int)date('Y', $date);
        $month = (int)date('m', $date);
        $day = (int)date('d', $date);
    }
    
    if ($calendar === 'jalali') {
        list($jy, $jm, $jd) = JalaliDate::toJalali($year, $month, $day);
        
        $monthNames = [
            1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور',
            'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند'
        ];

        $monthNamesShort = [
            1 => 'فرو', 'ارد', 'خرد', 'تیر', 'مرد', 'شهر',
            'مهر', 'آبان', 'آذر', 'دی', 'بهم', 'اسف'
        ];

        $replacements = [
            'Y' => $jy,
            'm' => str_pad($jm, 2, '0', STR_PAD_LEFT),
            'd' => str_pad($jd, 2, '0', STR_PAD_LEFT),
            'F' => $monthNames[$jm],
            'M' => $monthNamesShort[$jm],
            'j' => $jd,
            'n' => $jm,
        ];

        $result = $format;
        foreach ($replacements as $key => $value) {
            $result = str_replace($key, $value, $result);
        }

        return $result;
    } else {
        return date($format, mktime(0, 0, 0, $month, $day, $year));
    }
}
