<?php

namespace Core;

class App
{

    private static Container $container;

    public static function bind($key, $resolver): void
    {
        static::$container->set($key, $resolver);
    }

    public static function resolve(string $key)
    {
        return static::$container->get($key);
    }

    public static function singleton($key, $resolver)
    {
        static::$container->singleton($key, $resolver);
    }

    public static function init(Container $container)
    {
        static::$container = $container;
    }
}
