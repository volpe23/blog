<?php

namespace Core;

use Core\Middleware\Authenticated;
use Core\Middleware\Guest;
use Exception;

class Middleware {
    public const MAP = [
        "auth" => Authenticated::class,
        "guest" => Guest::class
    ];
    public static function resolve($key) {
        if (!$key) return;

        $middleware = self::MAP[$key] ?? false;

        if (!$middleware) throw new Exception("no middleware matches for key $key");

        (new $middleware)->handle();
    }
}