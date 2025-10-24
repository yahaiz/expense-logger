<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/database.php';

class DatabaseTest extends TestCase
{
    private $db;
    private $testDbPath;

    protected function setUp(): void
    {
        // Create a test database in memory
        $this->testDbPath = ':memory:';
        $this->db = new PDO('sqlite:' . $this->testDbPath);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Initialize test database schema
        $this->db->exec("
            CREATE TABLE categories (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name TEXT NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            )
        ");

        $this->db->exec("
            CREATE TABLE expenses (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                amount REAL NOT NULL,
                category_id INTEGER NOT NULL,
                date DATE NOT NULL,
                note TEXT,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
            )
        ");
    }

    protected function tearDown(): void
    {
        $this->db = null;
    }

    public function testDatabaseConnection()
    {
        $this->assertInstanceOf(PDO::class, $this->db);
    }

    public function testInsertAndRetrieveCategory()
    {
        // Insert a category
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute(['Test Category']);
        $categoryId = $this->db->lastInsertId();

        // Retrieve the category
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ?");
        $stmt->execute([$categoryId]);
        $category = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals('Test Category', $category['name']);
        $this->assertEquals($categoryId, $category['id']);
    }

    public function testInsertAndRetrieveExpense()
    {
        // Insert a category first
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute(['Food']);
        $categoryId = $this->db->lastInsertId();

        // Insert an expense
        $stmt = $this->db->prepare("INSERT INTO expenses (amount, category_id, date, note) VALUES (?, ?, ?, ?)");
        $stmt->execute([25.50, $categoryId, '2025-10-24', 'Test expense']);
        $expenseId = $this->db->lastInsertId();

        // Retrieve the expense
        $stmt = $this->db->prepare("SELECT * FROM expenses WHERE id = ?");
        $stmt->execute([$expenseId]);
        $expense = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(25.50, $expense['amount']);
        $this->assertEquals($categoryId, $expense['category_id']);
        $this->assertEquals('2025-10-24', $expense['date']);
        $this->assertEquals('Test expense', $expense['note']);
    }

    public function testCategoryExpenseRelationship()
    {
        // Insert a category
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute(['Transport']);
        $categoryId = $this->db->lastInsertId();

        // Insert multiple expenses for the category
        $expenses = [
            [15.00, 'Bus ticket'],
            [25.00, 'Taxi ride'],
            [50.00, 'Gas refill']
        ];

        foreach ($expenses as $expense) {
            $stmt = $this->db->prepare("INSERT INTO expenses (amount, category_id, date, note) VALUES (?, ?, ?, ?)");
            $stmt->execute([$expense[0], $categoryId, '2025-10-24', $expense[1]]);
        }

        // Calculate total expenses for category
        $stmt = $this->db->prepare("SELECT SUM(amount) as total FROM expenses WHERE category_id = ?");
        $stmt->execute([$categoryId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(90.00, $result['total']);
    }

    public function testPreparedStatementSecurity()
    {
        // Test that prepared statements prevent SQL injection
        $stmt = $this->db->prepare("INSERT INTO categories (name) VALUES (?)");
        $maliciousInput = "'; DROP TABLE categories; --";
        $stmt->execute([$maliciousInput]);

        // Check that table still exists and has the malicious input as data
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM categories");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $this->assertEquals(1, $result['count']);

        // Verify the malicious input was stored as literal string
        $stmt = $this->db->query("SELECT name FROM categories LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->assertEquals($maliciousInput, $result['name']);
    }
}