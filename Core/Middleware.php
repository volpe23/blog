<?php

namespace Core;

use Core\Middleware\Authenticated;
use Exception;

class Middleware {
    public const MAP = [
        "auth" => Authenticated::class,
        "guest"
    ];
    public static function resolve($key) {
        if (!$key) return;

        $middleware = self::MAP[$key] ?? false;

        if (!$middleware) throw new Exception("no middleware matches for key $key");

        (new $middleware)->handle();
    }
}