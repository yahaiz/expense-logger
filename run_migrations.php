<?php
/**
 * Migration Runner Web Interface
 * ExpenseLogger - خرج‌نگار
 * Web interface for running database migrations
 */

require_once __DIR__ . '/config/init.php';

// Require admin access
requireAdmin();

$pageTitle = 'Database Migrations';

require_once __DIR__ . '/migrate.php';
$migrationRunner = new MigrationRunner();

// Handle migration actions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    try {
        if ($action === 'run_migrations') {
            ob_start();
            $migrationRunner->runMigrations();
            $output = ob_get_clean();
            $message = "Migrations executed successfully.";
            $messageType = 'success';
        } elseif ($action === 'rollback') {
            ob_start();
            $migrationRunner->rollbackLast();
            $output = ob_get_clean();
            $message = "Last migration rolled back successfully.";
            $messageType = 'success';
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $messageType = 'error';
    }
}

$executed = $migrationRunner->getExecutedMigrations();
$available = $migrationRunner->getAvailableMigrations();
$pending = array_diff($available, $executed);

include __DIR__ . '/includes/header.php';
?>

<div class="space-y-6 animate-slide-in">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-3xl font-bold text-base-content">
                <i class="fas fa-database text-blue-600"></i> Database Migrations
            </h1>
            <p class="text-base-content/70 mt-1">Manage database schema changes and versioning</p>
        </div>
        <a href="admin.php" class="btn btn-ghost">
            <i class="fas fa-arrow-left"></i> Back to Admin
        </a>
    </div>

    <!-- Status Message -->
    <?php if ($message): ?>
        <div class="alert alert-<?php echo $messageType; ?>">
            <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-triangle'; ?>"></i>
            <span><?php echo h($message); ?></span>
        </div>
    <?php endif; ?>

    <!-- Migration Status -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Statistics -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg">Migration Status</h2>
                <div class="stats stats-vertical shadow">
                    <div class="stat">
                        <div class="stat-title">Executed</div>
                        <div class="stat-value text-success"><?php echo count($executed); ?></div>
                    </div>
                    <div class="stat">
                        <div class="stat-title">Pending</div>
                        <div class="stat-value <?php echo count($pending) > 0 ? 'text-warning' : 'text-success'; ?>">
                            <?php echo count($pending); ?>
                        </div>
                    </div>
                    <div class="stat">
                        <div class="stat-title">Total</div>
                        <div class="stat-value"><?php echo count($available); ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg">Actions</h2>
                <div class="space-y-2">
                    <form method="POST" action="run_migrations.php" class="inline">
                        <input type="hidden" name="action" value="run_migrations">
                        <button type="submit" class="btn btn-primary btn-block"
                                <?php echo count($pending) === 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-play"></i>
                            Run Pending Migrations
                            <?php if (count($pending) > 0): ?>
                                <span class="badge badge-sm"><?php echo count($pending); ?></span>
                            <?php endif; ?>
                        </button>
                    </form>

                    <form method="POST" action="run_migrations.php" class="inline">
                        <input type="hidden" name="action" value="rollback">
                        <button type="submit" class="btn btn-warning btn-block"
                                onclick="return confirm('Are you sure you want to rollback the last migration? This may cause data loss.')"
                                <?php echo count($executed) === 0 ? 'disabled' : ''; ?>>
                            <i class="fas fa-undo"></i>
                            Rollback Last Migration
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- CLI Commands -->
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title text-lg">CLI Commands</h2>
                <div class="space-y-2 text-sm font-mono bg-base-200 p-3 rounded">
                    <div>php migrate.php status</div>
                    <div>php migrate.php run</div>
                    <div>php migrate.php rollback</div>
                </div>
                <p class="text-xs text-base-content/70 mt-2">
                    Use these commands in terminal for advanced migration management.
                </p>
            </div>
        </div>
    </div>

    <!-- Migration History -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-history text-green-600"></i>
                Migration History
            </h2>

            <?php if (!empty($executed)): ?>
                <div class="overflow-x-auto">
                    <table class="table table-zebra">
                        <thead>
                            <tr>
                                <th>Migration</th>
                                <th>Executed At</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $history = $db->query("SELECT * FROM migrations ORDER BY executed_at DESC")->fetchAll();
                            foreach ($history as $migration):
                            ?>
                                <tr>
                                    <td class="font-mono text-sm"><?php echo h($migration['migration_name']); ?></td>
                                    <td><?php echo formatDate($migration['executed_at'], 'M d, Y H:i'); ?></td>
                                    <td>
                                        <span class="badge badge-<?php echo $migration['status'] === 'success' ? 'success' : 'error'; ?>">
                                            <?php echo h($migration['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-base-content/50">
                    <i class="fas fa-history text-4xl mb-4"></i>
                    <p>No migrations have been executed yet</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Migrations -->
    <?php if (!empty($pending)): ?>
        <div class="card bg-base-100 shadow-xl">
            <div class="card-body">
                <h2 class="card-title">
                    <i class="fas fa-clock text-warning"></i>
                    Pending Migrations
                </h2>
                <div class="space-y-2">
                    <?php foreach ($pending as $migration): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span class="font-mono"><?php echo h($migration); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Migration Files -->
    <div class="card bg-base-100 shadow-xl">
        <div class="card-body">
            <h2 class="card-title">
                <i class="fas fa-file-code text-purple-600"></i>
                Available Migration Files
            </h2>

            <?php if (!empty($available)): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <?php foreach ($available as $migration): ?>
                        <div class="card bg-base-200">
                            <div class="card-body p-4">
                                <h3 class="card-title text-sm font-mono"><?php echo h($migration); ?></h3>
                                <div class="flex justify-between items-center">
                                    <span class="text-xs text-base-content/70">
                                        <?php
                                        $isExecuted = in_array($migration, $executed);
                                        echo $isExecuted ? 'Executed' : 'Pending';
                                        ?>
                                    </span>
                                    <span class="badge badge-sm <?php echo $isExecuted ? 'badge-success' : 'badge-warning'; ?>">
                                        <?php echo $isExecuted ? '✓' : '○'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-8 text-base-content/50">
                    <i class="fas fa-file-code text-4xl mb-4"></i>
                    <p>No migration files found</p>
                    <p class="text-sm">Create migrations using the generate_migration.php script</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>