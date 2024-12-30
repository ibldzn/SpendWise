<?php

namespace App\Helpers;

class Redirect
{
    /**
     * Redirect to a given URL
     *
     * @param string $url The URL to redirect to
     * @param int $statusCode The status code of the redirect (default: 302)
    */
    public static function to(string $url, int $statusCode = 302): void
    {
        header("Location: {$_ENV['APP_URL']}/{$url}", true, $statusCode);
    }

    /**
     * Redirect back to the previous page
    */
    public static function back(): void
    {
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }

    /**
     * Redirect with a session key-value pair
     *
     * @param string $key The key of the session value
     * @param string $value The value of the session key
     * @return static
    */
    public static function with(string $key, string $value): static
    {
        Session::set($key, $value);
        return new static();
    }

    /**
     * Redirect with a flash message
     *
     * @param string $message The flash message to set
     * @return static
    */
    public static function withFlash(string $message): static
    {
        return self::with('flash', $message);
    }
}
