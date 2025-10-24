<?php
/**
 * Rate Limiting Middleware
 * ExpenseLogger - خرج‌نگار
 */

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\RateLimiter\Storage\InMemoryStorage;

class RateLimiter
{
    private static $limiters = [];

    public static function check($key, $limit = 100, $interval = 3600)
    {
        $storage = new InMemoryStorage();

        $factory = new RateLimiterFactory([
            'id' => $key,
            'policy' => 'sliding_window',
            'limit' => $limit,
            'interval' => $interval . ' seconds',
        ], $storage);

        $limiter = $factory->create($key);

        if (!$limiter->consume(1)->isAccepted()) {
            return false;
        }

        return true;
    }

    public static function getRemainingAttempts($key, $limit = 100, $interval = 3600)
    {
        // This is a simplified implementation
        // In a real application, you'd use persistent storage
        return $limit; // For testing purposes, always return full limit
    }
}