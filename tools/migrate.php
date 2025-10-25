<?php
/**
 * Database Migration System
 * ExpenseLogger - خرج‌نگار
 * Manages database schema changes and versioning
 */

require_once __DIR__ . '/config/init.php';

class MigrationRunner {
    private $db;
    private $migrationsTable = 'migrations';

    public function __construct() {
        $this->db = Database::getInstance();
        $this->ensureMigrationsTable();
    }

    /**
     * Ensure the migrations table exists
     */
    private function ensureMigrationsTable() {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS {$this->migrationsTable} (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                migration_name TEXT NOT NULL UNIQUE,
                executed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                status TEXT DEFAULT 'success'
            )
        ");
    }

    /**
     * Get list of executed migrations
     */
    public function getExecutedMigrations() {
        $result = $this->db->query("SELECT migration_name FROM {$this->migrationsTable} ORDER BY id");
        return array_column($result->fetchAll(), 'migration_name');
    }

    /**
     * Get list of available migration files
     */
    public function getAvailableMigrations() {
        $migrations = [];
        $files = glob(__DIR__ . '/migrations/*.php');

        foreach ($files as $file) {
            $filename = basename($file, '.php');
            if (preg_match('/^\d{4}_\d{2}_\d{2}_\d{6}_(.+)$/', $filename, $matches)) {
                $migrations[] = $filename;
            }
        }

        sort($migrations);
        return $migrations;
    }

    /**
     * Run pending migrations
     */
    public function runMigrations() {
        $executed = $this->getExecutedMigrations();
        $available = $this->getAvailableMigrations();
        $pending = array_diff($available, $executed);

        if (empty($pending)) {
            echo "No pending migrations.\n";
            return;
        }

        echo "Found " . count($pending) . " pending migration(s).\n";

        foreach ($pending as $migration) {
            echo "Running migration: {$migration}\n";

            try {
                $this->runMigration($migration);
                $this->recordMigration($migration, 'success');
                echo "✓ Migration {$migration} completed successfully.\n";
            } catch (Exception $e) {
                $this->recordMigration($migration, 'failed');
                echo "✗ Migration {$migration} failed: " . $e->getMessage() . "\n";
                logActivity('Migration failed', ['migration' => $migration, 'error' => $e->getMessage()]);
                break; // Stop on first failure
            }
        }
    }

    /**
     * Run a specific migration
     */
    private function runMigration($migrationName) {
        $file = __DIR__ . "/migrations/{$migrationName}.php";

        if (!file_exists($file)) {
            throw new Exception("Migration file not found: {$file}");
        }

        require_once $file;

        // Extract class name from filename
        $parts = explode('_', $migrationName, 5);
        $className = 'Migration_' . $parts[4];

        if (!class_exists($className)) {
            throw new Exception("Migration class not found: {$className}");
        }

        $migration = new $className();
        $migration->up();
    }

    /**
     * Record migration execution
     */
    private function recordMigration($migrationName, $status) {
        $this->db->query(
            "INSERT INTO {$this->migrationsTable} (migration_name, status) VALUES (?, ?)",
            [$migrationName, $status]
        );
    }

    /**
     * Rollback last migration
     */
    public function rollbackLast() {
        $lastMigration = $this->db->query(
            "SELECT migration_name FROM {$this->migrationsTable} WHERE status = 'success' ORDER BY id DESC LIMIT 1"
        )->fetch();

        if (!$lastMigration) {
            echo "No migrations to rollback.\n";
            return;
        }

        $migrationName = $lastMigration['migration_name'];
        echo "Rolling back migration: {$migrationName}\n";

        try {
            $this->rollbackMigration($migrationName);
            $this->db->query("DELETE FROM {$this->migrationsTable} WHERE migration_name = ?", [$migrationName]);
            echo "✓ Migration {$migrationName} rolled back successfully.\n";
        } catch (Exception $e) {
            echo "✗ Rollback failed: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Rollback a specific migration
     */
    private function rollbackMigration($migrationName) {
        $file = __DIR__ . "/migrations/{$migrationName}.php";

        if (!file_exists($file)) {
            throw new Exception("Migration file not found: {$file}");
        }

        require_once $file;

        // Extract class name from filename
        $parts = explode('_', $migrationName, 5);
        $className = 'Migration_' . $parts[4];

        if (!class_exists($className)) {
            throw new Exception("Migration class not found: {$className}");
        }

        $migration = new $className();
        if (method_exists($migration, 'down')) {
            $migration->down();
        } else {
            throw new Exception("Migration {$className} does not have a down() method");
        }
    }

    /**
     * Get migration status
     */
    public function getStatus() {
        $executed = $this->getExecutedMigrations();
        $available = $this->getAvailableMigrations();

        echo "Migration Status:\n";
        echo "================\n\n";

        echo "Executed migrations:\n";
        foreach ($executed as $migration) {
            echo "  ✓ {$migration}\n";
        }

        echo "\nPending migrations:\n";
        $pending = array_diff($available, $executed);
        foreach ($pending as $migration) {
            echo "  ○ {$migration}\n";
        }

        if (empty($pending)) {
            echo "  (none)\n";
        }
    }
}

/**
 * Base Migration Class
 */
abstract class Migration {
    protected $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * Run the migration
     */
    abstract public function up();

    /**
     * Rollback the migration (optional)
     */
    public function down() {
        // Default implementation - subclasses should override
        throw new Exception("Rollback not implemented for this migration");
    }
}

// CLI Interface
if (isset($argv) && $runner = new MigrationRunner()) {
    $command = $argv[1] ?? 'status';

    switch ($command) {
        case 'run':
            $runner->runMigrations();
            break;
        case 'rollback':
            $runner->rollbackLast();
            break;
        case 'status':
        default:
            $runner->getStatus();
            break;
    }
}
?>