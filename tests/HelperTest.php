<?php

use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    protected function setUp(): void
    {
        // Start session for tests if not already started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    protected function tearDown(): void
    {
        // Clean up session
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
    }

    public function testSanitizeInput()
    {
        require_once __DIR__ . '/../config/init.php';
        $input = "  <script>alert('xss')</script>  ";
        $expected = "&lt;script&gt;alert(&#039;xss&#039;)&lt;/script&gt;";
        $this->assertEquals($expected, sanitizeInput($input));
    }

    public function testHFunction()
    {
        require_once __DIR__ . '/../config/init.php';
        $input = "<b>Bold text</b>";
        $expected = "&lt;b&gt;Bold text&lt;/b&gt;";
        $this->assertEquals($expected, h($input));
    }

    public function testFormatCurrencyUSD()
    {
        require_once __DIR__ . '/../config/init.php';
        $_SESSION['currency'] = 'USD';
        $amount = 100000;
        $expected = '<span class="currency-sign currency-usd">$</span>1.00';
        $this->assertEquals($expected, formatCurrency($amount));
    }

    public function testFormatCurrencyToman()
    {
        require_once __DIR__ . '/../config/init.php';
        $_SESSION['currency'] = 'تومان';
        $amount = 50000;
        $expected = '50,000 <span class="currency-sign currency-toman">ت</span>';
        $this->assertEquals($expected, formatCurrency($amount));
    }

    public function testFormatCurrencyThousandToman()
    {
        require_once __DIR__ . '/../config/init.php';
        $_SESSION['currency'] = 'هزار تومان';
        $amount = 5000000;
        $expected = '5,000 <span class="currency-sign currency-toman">هزار ت</span>';
        $this->assertEquals($expected, formatCurrency($amount));
    }

    public function testFormatDateGregorian()
    {
        require_once __DIR__ . '/../config/init.php';
        $_SESSION['calendar'] = 'gregorian';
        $date = '2025-10-24';
        $expected = '24 Oct 2025';
        $this->assertEquals($expected, formatDate($date));
    }

    public function testFormatDateJalali()
    {
        require_once __DIR__ . '/../config/init.php';
        $_SESSION['calendar'] = 'jalali';
        $date = '2025-10-24';
        $result = formatDate($date);
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    public function testSetFlashMessage()
    {
        require_once __DIR__ . '/../config/init.php';
        setFlashMessage('success', 'Test message');
        $this->assertEquals('success', $_SESSION['flash_message']['type']);
        $this->assertEquals('Test message', $_SESSION['flash_message']['message']);
    }

    public function testGetFlashMessage()
    {
        require_once __DIR__ . '/../config/init.php';
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Test error'
        ];

        $flash = getFlashMessage();
        $this->assertEquals('error', $flash['type']);
        $this->assertEquals('Test error', $flash['message']);

        // Should be cleared after retrieval
        $this->assertNull(getFlashMessage());
    }

    public function testRedirectFunction()
    {
        require_once __DIR__ . '/../config/init.php';
        $this->assertTrue(function_exists('redirect'));
    }
}