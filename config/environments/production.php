<?php
/**
 * Production Environment Configuration
 * Overrides for production environment
 */

return [
    'app' => [
        'debug' => false,
        'maintenance' => false,
        'url' => 'https://yourdomain.com', // Change this to your actual domain
    ],

    'logging' => [
        'level' => 'WARNING',
        'log_requests' => false, // Disable request logging in production for performance
    ],

    'security' => [
        'session_lifetime' => 3600 * 24 * 30, // 30 days
        'rate_limit_requests' => 10,
        'encryption_key' => getenv('ENCRYPTION_KEY') ?: 'change-this-in-production-256-bit-key',
    ],

    'cache' => [
        'enabled' => true,
        'ttl' => 3600 * 24, // 24 hours
    ],

    'email' => [
        'enabled' => true,
        'smtp_host' => getenv('SMTP_HOST') ?: '',
        'smtp_port' => getenv('SMTP_PORT') ?: 587,
        'smtp_username' => getenv('SMTP_USERNAME') ?: '',
        'smtp_password' => getenv('SMTP_PASSWORD') ?: '',
    ],

    'services' => [
        'sentry_dsn' => getenv('SENTRY_DSN') ?: '',
        'google_analytics' => getenv('GA_TRACKING_ID') ?: '',
    ],
];