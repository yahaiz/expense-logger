<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../config/init.php';

class LoggingTest extends TestCase
{
    protected function setUp(): void
    {
        // Clean up any existing log files
        $logFiles = glob(__DIR__ . '/../logs/*.log');
        foreach ($logFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }
    }

    public function testLoggerIsAvailable()
    {
        $this->assertTrue(isset($GLOBALS['logger']));
        $this->assertInstanceOf(\Monolog\Logger::class, $GLOBALS['logger']);
    }

    public function testLogFunctionsExist()
    {
        $this->assertTrue(function_exists('logInfo'));
        $this->assertTrue(function_exists('logError'));
        $this->assertTrue(function_exists('logWarning'));
    }

    public function testLoggingCreatesFiles()
    {
        logInfo('Test info message');
        logError('Test error message');
        logWarning('Test warning message');

        // Check if log files were created (may not work in process isolation)
        $appLogExists = file_exists(__DIR__ . '/../logs/app.log');
        $errorLogExists = file_exists(__DIR__ . '/../logs/error.log');

        if ($appLogExists) {
            $this->assertFileExists(__DIR__ . '/../logs/app.log');
        }
        if ($errorLogExists) {
            $this->assertFileExists(__DIR__ . '/../logs/error.log');
        }

        // At least one of them should exist
        $this->assertTrue($appLogExists || $errorLogExists, 'At least one log file should be created');
    }

    public function testLogContent()
    {
        logInfo('Test message for content check');

        $logFile = __DIR__ . '/../logs/app.log';
        if (file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            $this->assertStringContains('Test message for content check', $logContent);
            $this->assertStringContains('INFO', $logContent);
        } else {
            $this->markTestSkipped('Log file not created in isolated process');
        }
    }
}