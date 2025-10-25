<?php
// Test config loading
try {
    require_once 'config/init.php';
    echo "Config loaded successfully\n";
    echo "Debug mode: " . (config('app.debug') ? 'true' : 'false') . "\n";
    echo "Environment: " . (getenv('APP_ENV') ?: 'development') . "\n";
} catch (Exception $e) {
    echo "Error loading config: " . $e->getMessage() . "\n";
}
?>