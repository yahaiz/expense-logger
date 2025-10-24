<?php
/**
 * Backup & Restore Page - backup.php
 * ExpenseLogger - خرج‌نگار
 * JSON export/import and database reset functionality
 */

require_once __DIR__ . '/config/init.php';

$pageTitle = 'Backup & Restore';
$db = Database::getInstance();

// Handle Export
if (isset($_GET['action']) && $_GET['action'] === 'export') {
    try {
        // Get all categories
        $categories = $db->query("SELECT * FROM categories ORDER BY id")->fetchAll();
        
        // Get all expenses
        $expenses = $db->query("SELECT * FROM expenses ORDER BY id")->fetchAll();
        
        $backup = [
            'export_date' => date('Y-m-d H:i:s'),
            'version' => '1.0',
            'categories' => $categories,
            'expenses' => $expenses
        ];
        
        $json = json_encode($backup, JSON_PRETTY_PRINT);
        
        header('Content-Type: application/json');
        header('Content-Disposition: attachment; filename="expenselogger_backup_' . date('Y-m-d_His') . '.json"');
        echo $json;
        exit;
    } catch (Exception $e) {
        setFlashMessage('error', 'Export failed: ' . $e->getMessage());
    }
}

// Handle Import
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'import') {
    if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] === UPLOAD_ERR_OK) {
        try {
            $jsonContent = file_get_contents($_FILES['backup_file']['tmp_name']);
            $backup = json_decode($jsonContent, true);
            
            if ($backup === null) {
                throw new Exception('Invalid JSON file');
            }
            
            if (!isset($backup['categories']) || !isset($backup['expenses'])) {
                throw new Exception('Invalid backup file format');
            }
            
            // Start transaction
            $db->beginTransaction();
            
            // Clear existing data if requested
            if (isset($_POST['clear_existing']) && $_POST['clear_existing'] === '1') {
                $db->query("DELETE FROM expenses");
                $db->query("DELETE FROM categories");
                $db->query("DELETE FROM sqlite_sequence WHERE name='expenses' OR name='categories'");
            }
            
            // Import categories
            $categoryMap = [];
            foreach ($backup['categories'] as $category) {
                // Check if category already exists
                $existing = $db->query(
                    "SELECT id FROM categories WHERE name = ?", 
                    [$category['name']]
                )->fetch();
                
                if ($existing) {
                    $categoryMap[$category['id']] = $existing['id'];
                } else {
                    $db->query(
                        "INSERT INTO categories (name) VALUES (?)",
                        [$category['name']]
                    );
                    $categoryMap[$category['id']] = $db->lastInsertId();
                }
            }
            
            // Import expenses
            $importedCount = 0;
            foreach ($backup['expenses'] as $expense) {
                $newCategoryId = $categoryMap[$expense['category_id']] ?? null;
                
                if ($newCategoryId) {
                    $db->query(
                        "INSERT INTO expenses (amount, category_id, date, note, created_at) VALUES (?, ?, ?, ?, ?)",
                        [
                            $expense['amount'],
                            $newCategoryId,
                            $expense['date'],
                            $expense['note'] ?? '',
                            $expense['created_at'] ?? date('Y-m-d H:i:s')
                        ]
                    );
                    $importedCount++;
                }
            }
            
            $db->commit();
            
            setFlashMessage('success', "Backup imported successfully! Imported $importedCount expense(s) and " . count($categoryMap) . " category(ies).");
            redirect('backup.php');
            
        } catch (Exception $e) {
            $db->rollback();
            setFlashMessage('error', 'Import failed: ' . $e->getMessage());
        }
    } else {
        setFlashMessage('error', 'Please select a valid backup file.');
    }
}

// Handle Database Reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'reset') {
    if (isset($_POST['confirm_reset']) && $_POST['confirm_reset'] === 'RESET') {
        try {
            $db->beginTransaction();
            
            $db->query("DELETE FROM expenses");
            $db->query("DELETE FROM categories");
            $db->query("DELETE FROM sqlite_sequence WHERE name='expenses' OR name='categories'");
            
            // Re-insert default categories
            $defaultCategories = ['Food', 'Transport', 'Health', 'Shopping', 'Other'];
            $stmt = $db->query("INSERT INTO categories (name) VALUES (?)", []);
            
            foreach ($defaultCategories as $category) {
                $db->query("INSERT INTO categories (name) VALUES (?)", [$category]);
            }
            
            $db->commit();
            
            setFlashMessage('success', 'Database reset successfully! Default categories have been restored.');
            redirect('backup.php');
            
        } catch (Exception $e) {
            $db->rollback();
            setFlashMessage('error', 'Reset failed: ' . $e->getMessage());
        }
    } else {
        setFlashMessage('error', 'Please type RESET to confirm database reset.');
    }
}

// Get database statistics
$stats = [
    'categories' => $db->query("SELECT COUNT(*) as count FROM categories")->fetch()['count'],
    'expenses' => $db->query("SELECT COUNT(*) as count FROM expenses")->fetch()['count'],
    'total_amount' => $db->query("SELECT COALESCE(SUM(amount), 0) as total FROM expenses")->fetch()['total'],
    'db_size' => file_exists(__DIR__ . '/data/expenselogger.db') ? filesize(__DIR__ . '/data/expenselogger.db') : 0
];

include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6 animate-slide-in">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-base-content">
            <i class="fas fa-database text-purple-600"></i> Backup & Restore
        </h1>
        <p class="text-base-content/70 mt-1">Manage your expense data safely</p>
    </div>

    <!-- Database Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-purple-600">
                    <i class="fas fa-tags text-2xl"></i>
                </div>
                <div class="stat-title">Categories</div>
                <div class="stat-value text-purple-600"><?php echo $stats['categories']; ?></div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-blue-600">
                    <i class="fas fa-receipt text-2xl"></i>
                </div>
                <div class="stat-title">Expenses</div>
                <div class="stat-value text-blue-600"><?php echo $stats['expenses']; ?></div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-emerald-600">
                    <i class="fas fa-dollar-sign text-2xl"></i>
                </div>
                <div class="stat-title">Total Amount</div>
                <div class="stat-value text-emerald-600 text-2xl">$<?php echo number_format($stats['total_amount'], 2); ?></div>
            </div>
        </div>

        <div class="stats shadow">
            <div class="stat">
                <div class="stat-figure text-orange-600">
                    <i class="fas fa-hdd text-2xl"></i>
                </div>
                <div class="stat-title">Database Size</div>
                <div class="stat-value text-orange-600 text-2xl"><?php echo number_format($stats['db_size'] / 1024, 2); ?> KB</div>
            </div>
        </div>
    </div>

    <!-- Export Backup -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl">
                <i class="fas fa-download text-blue-600"></i>
                Export Backup
            </h2>
            <p class="text-base-content/70">
                Download all your expense data as a JSON file. This backup includes all categories and expenses.
            </p>
            
            <div class="alert alert-info mt-4">
                <i class="fas fa-info-circle"></i>
                <div>
                    <h4 class="font-bold">What's included:</h4>
                    <ul class="list-disc list-inside text-sm">
                        <li><?php echo $stats['categories']; ?> categories</li>
                        <li><?php echo $stats['expenses']; ?> expense records</li>
                        <li>Export date and version info</li>
                    </ul>
                </div>
            </div>

            <div class="card-actions justify-end mt-4">
                <a href="backup.php?action=export" class="btn btn-primary btn-lg">
                    <i class="fas fa-download"></i> Download Backup
                </a>
            </div>
        </div>
    </div>

    <!-- Import Backup -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title text-2xl">
                <i class="fas fa-upload text-emerald-600"></i>
                Import Backup
            </h2>
            <p class="text-base-content/70">
                Restore your data from a previously exported JSON backup file.
            </p>

            <div class="alert alert-warning mt-4">
                <i class="fas fa-exclamation-triangle"></i>
                <div>
                    <h4 class="font-bold">Important Notes:</h4>
                    <ul class="list-disc list-inside text-sm">
                        <li>Only JSON files exported from ExpenseLogger are supported</li>
                        <li>Categories with the same name will be merged</li>
                        <li>You can choose to clear existing data before importing</li>
                    </ul>
                </div>
            </div>

            <form method="POST" action="backup.php" enctype="multipart/form-data" class="mt-4">
                <input type="hidden" name="action" value="import">
                
                <div class="form-control">
                    <label class="label">
                        <span class="label-text font-semibold">Select Backup File</span>
                    </label>
                    <input type="file" name="backup_file" accept=".json" 
                           class="file-input file-input-bordered w-full" required>
                </div>

                <div class="form-control mt-4">
                    <label class="label cursor-pointer justify-start gap-4">
                        <input type="checkbox" name="clear_existing" value="1" class="checkbox checkbox-warning">
                        <div>
                            <span class="label-text font-semibold">Clear existing data before import</span>
                            <p class="text-xs text-base-content/70">Warning: This will delete all current data</p>
                        </div>
                    </label>
                </div>

                <div class="card-actions justify-end mt-6">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-upload"></i> Import Backup
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Reset Database -->
    <div class="card bg-base-100 shadow-xl border-2 border-error">
        <div class="card-body">
            <h2 class="card-title text-2xl text-error">
                <i class="fas fa-exclamation-triangle"></i>
                Reset Database
            </h2>
            <p class="text-base-content/70">
                Permanently delete all data and reset the database to its initial state.
            </p>

            <div class="alert alert-error mt-4">
                <i class="fas fa-skull-crossbones"></i>
                <div>
                    <h4 class="font-bold">⚠️ DANGER ZONE ⚠️</h4>
                    <ul class="list-disc list-inside text-sm">
                        <li>This action is IRREVERSIBLE</li>
                        <li>All expenses and custom categories will be permanently deleted</li>
                        <li>Default categories will be restored</li>
                        <li>Make sure to export a backup before resetting!</li>
                    </ul>
                </div>
            </div>

            <button onclick="showResetModal()" class="btn btn-error btn-lg mt-4">
                <i class="fas fa-trash-alt"></i> Reset Database
            </button>
        </div>
    </div>

    <!-- Usage Guide -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-question-circle text-info"></i>
                How to Use
            </h2>
            <div class="space-y-4 text-sm">
                <div class="collapse collapse-arrow bg-base-200">
                    <input type="checkbox" />
                    <div class="collapse-title font-medium">
                        <i class="fas fa-download text-blue-600"></i> Creating a Backup
                    </div>
                    <div class="collapse-content">
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Click the "Download Backup" button</li>
                            <li>Save the JSON file to a safe location</li>
                            <li>The filename includes the date and time of export</li>
                            <li>Keep multiple backups for different time periods</li>
                        </ol>
                    </div>
                </div>

                <div class="collapse collapse-arrow bg-base-200">
                    <input type="checkbox" />
                    <div class="collapse-title font-medium">
                        <i class="fas fa-upload text-emerald-600"></i> Restoring from Backup
                    </div>
                    <div class="collapse-content">
                        <ol class="list-decimal list-inside space-y-2">
                            <li>Click "Choose File" and select your backup JSON file</li>
                            <li>Decide if you want to clear existing data (optional)</li>
                            <li>Click "Import Backup" to restore</li>
                            <li>Wait for the confirmation message</li>
                        </ol>
                    </div>
                </div>

                <div class="collapse collapse-arrow bg-base-200">
                    <input type="checkbox" />
                    <div class="collapse-title font-medium">
                        <i class="fas fa-sync text-orange-600"></i> Best Practices
                    </div>
                    <div class="collapse-content">
                        <ul class="list-disc list-inside space-y-2">
                            <li>Export backups regularly (weekly or monthly)</li>
                            <li>Store backups in multiple locations (cloud, USB drive)</li>
                            <li>Test your backups by importing them occasionally</li>
                            <li>Always export before major changes or database reset</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reset Confirmation Modal -->
<dialog id="resetModal" class="modal">
    <div class="modal-box w-11/12 max-w-lg border-4 border-error">
        <h3 class="font-bold text-2xl text-error mb-4">
            <i class="fas fa-exclamation-triangle"></i> CONFIRM DATABASE RESET
        </h3>
        
        <div class="alert alert-error mb-4">
            <i class="fas fa-radiation"></i>
            <span class="font-bold">This action cannot be undone!</span>
        </div>

        <p class="mb-4">You are about to delete:</p>
        <ul class="list-disc list-inside mb-4 bg-base-200 p-4 rounded">
            <li class="text-error font-bold"><?php echo $stats['expenses']; ?> expense records</li>
            <li class="text-error font-bold"><?php echo $stats['categories']; ?> categories</li>
            <li class="text-error font-bold">$<?php echo number_format($stats['total_amount'], 2); ?> worth of tracked expenses</li>
        </ul>

        <form method="POST" action="backup.php">
            <input type="hidden" name="action" value="reset">
            
            <div class="form-control">
                <label class="label">
                    <span class="label-text font-bold">Type "RESET" to confirm:</span>
                </label>
                <input type="text" name="confirm_reset" class="input input-bordered input-error" 
                       placeholder="Type RESET in capital letters" required>
            </div>

            <div class="modal-action">
                <button type="button" class="btn" onclick="document.getElementById('resetModal').close()">
                    Cancel
                </button>
                <button type="submit" class="btn btn-error">
                    <i class="fas fa-trash-alt"></i> Yes, Reset Everything
                </button>
            </div>
        </form>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
function showResetModal() {
    document.getElementById('resetModal').showModal();
}
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
