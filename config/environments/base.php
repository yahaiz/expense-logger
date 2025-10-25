<?php
/**
 * Base Configuration
 * Shared configuration for all environments
 */

return [
    // Application
    'app' => [
        'name' => 'ExpenseLogger',
        'version' => '2.0.0',
        'url' => 'http://localhost/ExpenseLogger',
        'timezone' => 'Asia/Tehran',
        'debug' => false,
        'maintenance' => false,
    ],

    // Database
    'database' => [
        'type' => 'sqlite',
        'path' => __DIR__ . '/../data/expenses.db',
        'charset' => 'utf8mb4',
    ],

    // Security
    'security' => [
        'session_lifetime' => 3600 * 24 * 7, // 7 days
        'password_min_length' => 8,
        'rate_limit_requests' => 10,
        'rate_limit_window' => 60, // seconds
        'csrf_token_lifetime' => 3600, // 1 hour
        'encryption_key' => 'your-256-bit-secret', // Change this in production
    ],

    // Logging
    'logging' => [
        'level' => 'INFO',
        'max_files' => 30,
        'max_file_size' => 10485760, // 10MB
        'log_requests' => true,
        'log_errors' => true,
    ],

    // Email (for future features)
    'email' => [
        'enabled' => false,
        'smtp_host' => '',
        'smtp_port' => 587,
        'smtp_username' => '',
        'smtp_password' => '',
        'from_email' => 'noreply@expenselogger.local',
        'from_name' => 'ExpenseLogger',
    ],

    // Features
    'features' => [
        'user_registration' => true,
        'admin_panel' => true,
        'api' => true,
        'backup' => true,
        'export' => true,
        'themes' => true,
        'multi_currency' => true,
        'multi_language' => false, // Future feature
    ],

    // Limits
    'limits' => [
        'max_expenses_per_page' => 50,
        'max_file_upload_size' => 5242880, // 5MB
        'max_backup_files' => 10,
        'max_log_files' => 30,
    ],

    // Cache
    'cache' => [
        'enabled' => false,
        'type' => 'file', // file, redis, memcached
        'ttl' => 3600, // 1 hour
        'path' => __DIR__ . '/../data/cache',
    ],

    // Third-party services
    'services' => [
        'google_analytics' => '',
        'sentry_dsn' => '',
        'backup_storage' => 'local', // local, s3, etc.
    ],
];