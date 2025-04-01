<?php

namespace Core;

use Error;

class Route
{

    private static $router;
    private static array $allowedMethods = ["GET", "POST"];

    public static function __callStatic($name, $arguments)
    {
        $method = strtoupper($name);
        if (!is_string($arguments[0])) throw new Error("provided URI is not a string");
        if (!in_array($method, self::$allowedMethods)) throw new Error("method not allowed");
        [$uri, $cb] = $arguments;

        if (is_array($cb) && !is_callable($cb) && isset($cb[0], $cb[1])) {
            $cb[0] = App::make($cb[0]);

            if (!is_callable($cb)) throw new Error("provided callback is not callable");
        }

        self::$router->$name($uri, $cb);
    }

    public static function setRouter($router)
    {
        self::$router = $router;
    }
}

Route::setRouter(App::make(Router::class));
