<?php

namespace Core;

use Error;

class App
{

    private static $container = [];

    public static function bind($key, $resolver): void
    {
        static::$container[$key] = $resolver;
    }

    public static function resolve($key)
    {
        if (!array_key_exists($key, static::$container)) throw new Error("key does not exist in container");

        return call_user_func(static::$container[$key]);
    }
}
