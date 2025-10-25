<?php
/**
 * Development Environment Configuration
 * Overrides for development environment
 */

return [
    'app' => [
        'debug' => true,
        'url' => 'http://localhost/ExpenseLogger',
    ],

    'logging' => [
        'level' => 'DEBUG',
        'log_requests' => true,
    ],

    'security' => [
        'rate_limit_requests' => 100, // More permissive for development
    ],

    'features' => [
        'user_registration' => true,
        'admin_panel' => true,
    ],

    'cache' => [
        'enabled' => false,
    ],
];