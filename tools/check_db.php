<?php
require_once 'config/init.php';

$db = Database::getInstance();

// Check users table
echo "Users table schema:\n";
$result = $db->query('PRAGMA table_info(users)')->fetchAll();
print_r($result);

// Check if categories has user_id
echo "\nCategories table schema:\n";
$result = $db->query('PRAGMA table_info(categories)')->fetchAll();
print_r($result);

// Check if expenses has user_id
echo "\nExpenses table schema:\n";
$result = $db->query('PRAGMA table_info(expenses)')->fetchAll();
print_r($result);

// Check migrations table
echo "\nMigrations table:\n";
$result = $db->query('SELECT * FROM migrations')->fetchAll();
print_r($result);
?>