<?php
/**
 * Initial Database Schema Migration
 * Creates users table and adds user_id to existing tables
 */

class Migration_InitialSchema extends Migration {
    public function up() {
        // Create users table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                username TEXT NOT NULL UNIQUE,
                email TEXT NOT NULL UNIQUE,
                full_name TEXT NOT NULL,
                password_hash TEXT NOT NULL,
                role TEXT DEFAULT 'user' CHECK (role IN ('user', 'admin')),
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Create indexes for users table
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_users_username ON users(username)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_users_email ON users(email)");

        // Add user_id to categories table
        $this->db->query("ALTER TABLE categories ADD COLUMN user_id INTEGER");

        // Add user_id to expenses table
        $this->db->query("ALTER TABLE expenses ADD COLUMN user_id INTEGER");

        // Create indexes for user_id columns
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_categories_user_id ON categories(user_id)");
        $this->db->query("CREATE INDEX IF NOT EXISTS idx_expenses_user_id ON expenses(user_id)");

        // Create default admin user (only if no users exist)
        $existingUsers = $this->db->query("SELECT COUNT(*) as count FROM users")->fetch()['count'];
        if ($existingUsers == 0) {
            $adminPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $this->db->query(
                "INSERT INTO users (username, email, full_name, password_hash, role) VALUES (?, ?, ?, ?, ?)",
                ['admin', 'admin@expenselogger.local', 'System Administrator', $adminPassword, 'admin']
            );
        }

        // Update existing categories to belong to admin user
        $adminUser = $this->db->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1")->fetch();
        if ($adminUser) {
            $this->db->query("UPDATE categories SET user_id = ? WHERE user_id IS NULL", [$adminUser['id']]);
            $this->db->query("UPDATE expenses SET user_id = ? WHERE user_id IS NULL", [$adminUser['id']]);
        }

        // Add foreign key constraints (SQLite doesn't enforce them by default, but good for documentation)
        // Note: SQLite foreign keys need to be enabled with PRAGMA foreign_keys = ON;

        echo "Initial schema migration completed.\n";
    }

    public function down() {
        // Remove user_id columns
        // Note: SQLite doesn't support DROP COLUMN, so we need to recreate tables

        // This is a simplified rollback - in production you'd want more careful handling
        echo "Warning: Rolling back initial schema will remove all user data!\n";

        // For safety, we'll just warn and not actually rollback
        // In a real system, you'd recreate tables without user_id columns
        throw new Exception("Initial schema migration cannot be safely rolled back");
    }
}
?>