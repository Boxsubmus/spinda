<?php

namespace App;

final class Env
{
    public static function raw(string $name, ?string $default = null): ?string {
        return $_ENV[$name] ?? $_SERVER[$name] ?? $default;
    }

    public static function string(string $name, string $fallback = ''): string {
        return self::raw($name) ?? $fallback;
    }
}