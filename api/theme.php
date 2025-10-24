<?php
/**
 * Theme Toggle API
 */
session_start();

header('Content-Type: application/json');

// Include rate limiter
require_once __DIR__ . '/../config/ratelimiter.php';

// Rate limiting: 10 requests per minute per IP
$clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
if (!RateLimiter::check('theme_api_' . $clientIP, 10, 60)) {
    http_response_code(429);
    logWarning('Rate limit exceeded for theme API', ['ip' => $clientIP]);
    echo json_encode(['error' => 'Too many requests. Please try again later.']);
    exit;
}

// Get allowed themes from themes.php
require_once __DIR__ . '/../config/theme-generator.php';
$allowed = getAllThemeKeys();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['theme']) && in_array($data['theme'], $allowed)) {
        $_SESSION['theme'] = $data['theme'];
        logInfo('Theme changed successfully', ['theme' => $data['theme'], 'ip' => $clientIP]);
        echo json_encode(['success' => true]);
    } else {
        logWarning('Invalid theme requested', ['theme' => $data['theme'] ?? 'none', 'ip' => $clientIP]);
        echo json_encode(['success' => false, 'error' => 'Invalid theme']);
    }
} else {
    logWarning('Invalid request method for theme API', ['method' => $_SERVER['REQUEST_METHOD'], 'ip' => $clientIP]);
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
