<?php
/**
 * Application Initialization
 * ExpenseLogger - خرج‌نگار
 */

// Set timezone
date_default_timezone_set('UTC');

// Error reporting based on config
if ($config['app']['debug'] ?? false) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
    ini_set('display_errors', 0);
}

// Start session with configured lifetime
if (session_status() === PHP_SESSION_NONE) {
    $sessionLifetime = $config['security']['session_lifetime'] ?? 3600;
    ini_set('session.gc_maxlifetime', $sessionLifetime);
    session_set_cookie_params($sessionLifetime);
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

// Check app access (lock system)
requireAppAccess();

// Include database configuration
require_once __DIR__ . '/database.php';

// Load environment configuration
function loadConfig() {
    $baseConfig = [];
    $baseConfigFile = __DIR__ . '/environments/base.php';

    if (!file_exists($baseConfigFile)) {
        // Fallback if base config doesn't exist
        $baseConfig = [
            'app' => ['debug' => true],
            'logging' => ['level' => 'INFO'],
            'security' => ['rate_limit_requests' => 10, 'session_lifetime' => 3600 * 24 * 7],
        ];
    } else {
        $baseConfig = require $baseConfigFile;
    }

    $environment = getenv('APP_ENV') ?: 'development';
    $envConfigFile = __DIR__ . "/environments/{$environment}.php";

    if (file_exists($envConfigFile)) {
        $envConfig = require $envConfigFile;
        return array_replace_recursive($baseConfig, $envConfig);
    }

    return $baseConfig;
}

// Load configuration
$config = loadConfig();

// Make config globally available
$GLOBALS['config'] = $config;

// Helper function to get config values
function config($key = null, $default = null) {
    global $config;

    if ($key === null) {
        return $config;
    }

    $keys = explode('.', $key);
    $value = $config;

    foreach ($keys as $k) {
        if (!isset($value[$k])) {
            return $default;
        }
        $value = $value[$k];
    }

    return $value;
}

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
$logLevel = constant("Monolog\Logger::" . strtoupper($config['logging']['level'] ?? 'INFO'));
$rotatingHandler = new RotatingFileHandler($logPath, 30, $logLevel);
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

// Helper function to format dates based on calendar setting
function formatDate($date, $format = 'd M Y') {
    if (!$date) return '';

    $calendar = $_SESSION['calendar'] ?? 'gregorian';

    if ($calendar === 'jalali') {
        // Convert Gregorian to Jalali
        $timestamp = strtotime($date);
        return jdate($format, $timestamp);
    } else {
        // Format Gregorian date
        $timestamp = strtotime($date);
        return date($format, $timestamp);
    }
}

// App Lock System - Simple password protection

// Helper function to get lock file paths
function getLockFilePaths() {
    $userDataPath = getenv('EXPENSELOGGER_USER_DATA');
    if ($userDataPath && is_dir($userDataPath)) {
        return [
            'password' => $userDataPath . '/.app_password',
            'lock' => $userDataPath . '/.app_locked'
        ];
    } else {
        return [
            'password' => __DIR__ . '/../.app_password',
            'lock' => __DIR__ . '/../.app_locked'
        ];
    }
}

// Helper function to check if app password is set
function isPasswordSet() {
    $paths = getLockFilePaths();
    return file_exists($paths['password']) && filesize($paths['password']) > 0;
}

// Helper function to check if app is locked
function isAppLocked() {
    $paths = getLockFilePaths();
    return file_exists($paths['lock']);
}

// Helper function to set app password
function setAppPassword($password) {
    if (empty($password)) {
        return false;
    }

    $paths = getLockFilePaths();
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return file_put_contents($paths['password'], $hash) !== false;
}

// Helper function to verify app password
function verifyAppPassword($password) {
    $paths = getLockFilePaths();
    if (!isPasswordSet()) {
        return false;
    }

    $hash = trim(file_get_contents($paths['password']));
    return password_verify($password, $hash);
}

// Helper function to lock the app
function lockApp() {
    $paths = getLockFilePaths();
    return file_put_contents($paths['lock'], time()) !== false;
}

// Helper function to unlock the app
function unlockApp() {
    $paths = getLockFilePaths();
    if (file_exists($paths['lock'])) {
        return unlink($paths['lock']);
    }
    return true;
}

// Helper function to require app access (check lock status)
function requireAppAccess() {
    // If password not set yet, redirect to unlock page for setup
    if (!isPasswordSet()) {
        $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if ($currentPage !== 'unlock.php' && !str_starts_with($requestUri, '/api/')) {
            redirect('unlock.php');
        }
        return;
    }

    // If app is locked, redirect to unlock page
    if (isAppLocked()) {
        // Allow access to unlock.php and API endpoints
        $currentPage = basename($_SERVER['PHP_SELF'] ?? '');
        $requestUri = $_SERVER['REQUEST_URI'] ?? '';
        if ($currentPage !== 'unlock.php' && !str_starts_with($requestUri, '/api/')) {
            redirect('unlock.php');
        }
    }
}
