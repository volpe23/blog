<?php

namespace Core;

use Core\Middleware\Authenticated;
use Core\Middleware\Csrf;
use Core\Middleware\Guest;
use Exception;

class Middleware
{
    /**
     * @var array<string, MiddlewareInterface> MAP
     */
    public const MAP = [
        "auth" => Authenticated::class,
        "guest" => Guest::class,
        "csrf" => Csrf::class
    ];

    /**
     * @return callable
     */
    public function resolve($key)
    {
        if (!$key) return;

        $middleware = self::MAP[$key] ?? false;

        if (!$middleware) throw new Exception("no middleware matches for key $key");

        return [new $middleware, "handle"];
    }
}
