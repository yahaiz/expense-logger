<?php
/**
 * Database Configuration
 * ExpenseLogger - خرج‌نگار
 * SQLite Database Connection Handler
 */

class Database {
    private static $instance = null;
    private $db;
    private $dbPath;

    private function __construct() {
        $this->dbPath = __DIR__ . '/../data/expenselogger.db';
        
        // Create data directory if it doesn't exist
        $dataDir = dirname($this->dbPath);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }

        try {
            $this->db = new PDO('sqlite:' . $this->dbPath);
            $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->initDatabase();
        } catch (PDOException $e) {
            die("Database connection failed: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->db;
    }

    private function initDatabase() {
        // Create categories table
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        // Create expenses table
        $this->db->exec("
            CREATE TABLE IF NOT EXISTS expenses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                amount REAL NOT NULL,
                category_id INTEGER NOT NULL,
                date DATE NOT NULL,
                note TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            )
        ");

        // Insert default categories if table is empty
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM categories");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            $defaultCategories = ['Food', 'Transport', 'Health', 'Shopping', 'Other'];
            $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
            
            foreach ($defaultCategories as $category) {
                $stmt->execute([$category]);
            }
        }
    }

    public function query($sql, $params = []) {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Log successful queries (excluding sensitive data)
            if (strpos(strtolower($sql), 'select') === 0) {
                logInfo('Database query executed', ['query_type' => 'SELECT']);
            } elseif (strpos(strtolower($sql), 'insert') === 0) {
                logInfo('Database insert executed', ['query_type' => 'INSERT']);
            } elseif (strpos(strtolower($sql), 'update') === 0) {
                logInfo('Database update executed', ['query_type' => 'UPDATE']);
            } elseif (strpos(strtolower($sql), 'delete') === 0) {
                logInfo('Database delete executed', ['query_type' => 'DELETE']);
            }
            
            return $stmt;
        } catch (PDOException $e) {
            logError('Database query error', [
                'error' => $e->getMessage(),
                'sql' => $sql,
                'params' => $params
            ]);
            error_log("Database query error: " . $e->getMessage());
            throw $e;
        }
    }

    public function lastInsertId() {
        return $this->db->lastInsertId();
    }

    public function beginTransaction() {
        return $this->db->beginTransaction();
    }

    public function commit() {
        return $this->db->commit();
    }

    public function rollback() {
        return $this->db->rollBack();
    }
}
