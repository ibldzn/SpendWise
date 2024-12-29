<?php

namespace App\Helpers;

class Redirect
{
    public static function to(string $url, int $statusCode = 302): void
    {
        header("Location: {$_ENV['APP_URL']}/{$url}", true, $statusCode);
    }

    public static function back(): void
    {
        header("Location: {$_SERVER['HTTP_REFERER']}");
    }

    public static function with(string $key, string $value): static
    {
        Session::set($key, $value);
        return new static();
    }
}
