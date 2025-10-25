<?php
/**
 * Sample Data Generator
 * Run this script once to populate the database with sample data for testing
 * Access via: http://localhost/ExpenseLogger/tools/demo_data.php
 */

require_once __DIR__ . '/config/init.php';

$db = Database::getInstance();

// Check if data already exists
$existingExpenses = $db->query("SELECT COUNT(*) as count FROM expenses")->fetch()['count'];

if ($existingExpenses > 0) {
    $message = "Database already contains $existingExpenses expense(s). Sample data not added.";
    $type = 'warning';
} else {
    try {
        // Sample expenses data
        $sampleExpenses = [
            // Food expenses
            ['amount' => 45.50, 'category' => 'Food', 'date' => date('Y-m-d', strtotime('-1 day')), 'note' => 'Grocery shopping at local market'],
            ['amount' => 12.99, 'category' => 'Food', 'date' => date('Y-m-d', strtotime('-2 days')), 'note' => 'Lunch at downtown restaurant'],
            ['amount' => 8.50, 'category' => 'Food', 'date' => date('Y-m-d', strtotime('-3 days')), 'note' => 'Coffee and breakfast'],
            ['amount' => 67.20, 'category' => 'Food', 'date' => date('Y-m-d', strtotime('-5 days')), 'note' => 'Weekly grocery shopping'],
            ['amount' => 25.00, 'category' => 'Food', 'date' => date('Y-m-d', strtotime('-7 days')), 'note' => 'Dinner with friends'],
            
            // Transport expenses
            ['amount' => 50.00, 'category' => 'Transport', 'date' => date('Y-m-d', strtotime('-1 day')), 'note' => 'Gas refill'],
            ['amount' => 15.00, 'category' => 'Transport', 'date' => date('Y-m-d', strtotime('-4 days')), 'note' => 'Taxi to airport'],
            ['amount' => 120.00, 'category' => 'Transport', 'date' => date('Y-m-d', strtotime('-10 days')), 'note' => 'Monthly bus pass'],
            ['amount' => 8.50, 'category' => 'Transport', 'date' => date('Y-m-d', strtotime('-15 days')), 'note' => 'Parking fee'],
            
            // Health expenses
            ['amount' => 35.00, 'category' => 'Health', 'date' => date('Y-m-d', strtotime('-2 days')), 'note' => 'Pharmacy - vitamins and supplements'],
            ['amount' => 150.00, 'category' => 'Health', 'date' => date('Y-m-d', strtotime('-12 days')), 'note' => 'Dental checkup'],
            ['amount' => 22.50, 'category' => 'Health', 'date' => date('Y-m-d', strtotime('-20 days')), 'note' => 'Prescription medication'],
            
            // Shopping expenses
            ['amount' => 89.99, 'category' => 'Shopping', 'date' => date('Y-m-d', strtotime('-3 days')), 'note' => 'New shoes'],
            ['amount' => 45.00, 'category' => 'Shopping', 'date' => date('Y-m-d', strtotime('-6 days')), 'note' => 'Books and magazines'],
            ['amount' => 199.99, 'category' => 'Shopping', 'date' => date('Y-m-d', strtotime('-14 days')), 'note' => 'Electronics accessories'],
            ['amount' => 32.50, 'category' => 'Shopping', 'date' => date('Y-m-d', strtotime('-18 days')), 'note' => 'Clothing store'],
            
            // Other expenses
            ['amount' => 75.00, 'category' => 'Other', 'date' => date('Y-m-d', strtotime('-5 days')), 'note' => 'Movie tickets and popcorn'],
            ['amount' => 50.00, 'category' => 'Other', 'date' => date('Y-m-d', strtotime('-8 days')), 'note' => 'Gift for friend birthday'],
            ['amount' => 120.00, 'category' => 'Other', 'date' => date('Y-m-d', strtotime('-16 days')), 'note' => 'Gym membership'],
            ['amount' => 15.99, 'category' => 'Other', 'date' => date('Y-m-d', strtotime('-22 days')), 'note' => 'Streaming subscription'],
            
            // More varied amounts and dates
            ['amount' => 28.75, 'category' => 'Food', 'date' => date('Y-m-d', strtotime('-25 days')), 'note' => 'Fast food delivery'],
            ['amount' => 95.00, 'category' => 'Transport', 'date' => date('Y-m-d', strtotime('-28 days')), 'note' => 'Uber rides this week'],
            ['amount' => 42.00, 'category' => 'Shopping', 'date' => date('Y-m-d', strtotime('-30 days')), 'note' => 'Online shopping'],
        ];
        
        // Get category IDs
        $categories = $db->query("SELECT id, name FROM categories")->fetchAll(PDO::FETCH_KEY_PAIR);
        
        // Insert sample expenses
        $stmt = $db->getConnection()->prepare(
            "INSERT INTO expenses (amount, category_id, user_id, date, note) VALUES (?, ?, ?, ?, ?)"
        );
        
        $insertedCount = 0;
        foreach ($sampleExpenses as $expense) {
            if (isset($categories[$expense['category']])) {
                $stmt->execute([
                    $expense['amount'],
                    $categories[$expense['category']],
                    1, // Use default admin user ID
                    $expense['date'],
                    $expense['note']
                ]);
                $insertedCount++;
            }
        }
        
        $message = "Successfully added $insertedCount sample expenses to the database!";
        $type = 'success';
        
    } catch (Exception $e) {
        $message = "Error adding sample data: " . $e->getMessage();
        $type = 'error';
    }
}

// Display result page
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sample Data Generator - ExpenseLogger</title>
    <link href="assets/css/daisyui.min.css" rel="stylesheet" type="text/css" />
    <script src="assets/js/tailwindcss.min.js"></script>
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
</head>
<body class="min-h-screen bg-base-200 flex items-center justify-center p-4">
    <div class="card w-full max-w-2xl bg-base-100 shadow-2xl">
        <div class="card-body">
            <h1 class="card-title text-3xl mb-4">
                <i class="fas fa-database text-purple-600"></i>
                Sample Data Generator
            </h1>
            
            <div class="alert alert-<?php echo $type; ?> mb-4">
                <i class="fas fa-<?php echo $type === 'success' ? 'check-circle' : ($type === 'warning' ? 'exclamation-triangle' : 'times-circle'); ?>"></i>
                <span><?php echo htmlspecialchars($message); ?></span>
            </div>
            
            <?php if ($type === 'success'): ?>
                <div class="bg-base-200 p-4 rounded-lg mb-4">
                    <h3 class="font-bold mb-2">Sample Data Includes:</h3>
                    <ul class="list-disc list-inside space-y-1 text-sm">
                        <li>23 expense records spread across 30 days</li>
                        <li>Various amounts from $8.50 to $199.99</li>
                        <li>All 5 default categories covered</li>
                        <li>Realistic notes and descriptions</li>
                    </ul>
                </div>
            <?php endif; ?>
            
            <div class="card-actions justify-end mt-4">
                <?php if ($type === 'warning'): ?>
                    <a href="backup.php" class="btn btn-warning">
                        <i class="fas fa-database"></i> Manage Database
                    </a>
                <?php endif; ?>
                <a href="index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i> Go to Dashboard
                </a>
            </div>
            
            <div class="divider"></div>
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <div>
                    <h4 class="font-bold">Note:</h4>
                    <p class="text-sm">
                        This script can only be run once. If you need to reset and reload sample data, 
                        use the "Reset Database" feature in the Backup & Restore page.
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
