<?php
/**
 * Migration Generator
 * Creates new migration files with timestamp
 */

if ($argc < 2) {
    echo "Usage: php generate_migration.php <migration_name>\n";
    echo "Example: php generate_migration.php add_budget_table\n";
    exit(1);
}

$migrationName = $argv[1];
$timestamp = date('Y_m_d_His');
$filename = "{$timestamp}_{$migrationName}.php";
$className = 'Migration_' . str_replace(' ', '', ucwords(str_replace('_', ' ', $migrationName)));

$template = "<?php
/**
 * {$migrationName} Migration
 * " . str_replace('_', ' ', ucwords($migrationName)) . "
 */

class {$className} extends Migration {
    public function up() {
        // TODO: Implement migration
        // Example:
        // \$this->db->query('CREATE TABLE example (id INTEGER PRIMARY KEY, name TEXT)');
    }

    public function down() {
        // TODO: Implement rollback
        // Example:
        // \$this->db->query('DROP TABLE example');
    }
}
";

file_put_contents(__DIR__ . "/migrations/{$filename}", $template);

echo "Migration created: migrations/{$filename}\n";
echo "Class name: {$className}\n";
?>