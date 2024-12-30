<?php

namespace App\Helpers;

class Session
{
    /**
     * Start the session
    */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Set a session key-value pair
     *
     * @param string $key The key of the session value
     * @param mixed $value The value of the session key
    */
    public static function set(string $key, mixed $value): void
    {
        self::start();
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value by key
     *
     * @param string $key The key of the session value
     * @return mixed
    */
    public static function get(string $key): mixed
    {
        self::start();
        return $_SESSION[$key] ?? null;
    }

    /**
     * Get a session value by key, or a default value if not found
     *
     * @param string $key The key of the session value
     * @param mixed $default The default value to return if the key is not found
     * @return mixed
    */
    public static function getOrDefault(string $key, mixed $default): mixed
    {
        self::start();
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Remove a session value by key
     *
     * @param string $key The key of the session value to remove
    */
    public static function remove(string $key): void
    {
        self::start();
        unset($_SESSION[$key]);
    }

    /**
     * Get all session values
     *
     * @return array All session values
    */
    public static function all(): array
    {
        self::start();
        return $_SESSION;
    }

    /**
     * Check if a session key exists
     *
     * @param string $key The key to check
     * @return bool true if the key exists, false otherwise
    */
    public static function has(string $key): bool
    {
        self::start();
        return isset($_SESSION[$key]);
    }

    /**
     * Destroy the session
    */
    public static function destroy(): void
    {
        self::start();
        session_destroy();
        $_SESSION = [];
    }
}
