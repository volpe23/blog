<?php

namespace Core;

use Error;
use ReflectionClass;

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

    public static function singleton($key, $resolver)
    {
        static::$container[$key] = function () use ($resolver) {
            static $instance;
            if (!$instance) {
                $instance = $resolver();
            }

            return $instance;
        };
    }

    public static function make($key)
    {

        if (isset(static::$container[$key])) {
            return static::resolve($key);
        }

        return static::build($key);
    }


    private static function build($className): object
    {
        if (!class_exists($className)) throw new Error("class does not exist");

        $reflector = new ReflectionClass($className);
        if (!$reflector->isInstantiable()) throw new Error("class is not instantiable");
        $constructor = $reflector->getConstructor();

        if (!$constructor) return $reflector->newInstance();
        $params = $constructor->getParameters();
        $dependencies = [];

        foreach ($params as $param) {
            $type = $param->getType();

            if (!$type || $type->isBuiltin()) {
                throw new Error("cannot resolve non-class dependency for $className");
            }
            $dependencies[] = static::make($type->getName());
        }

        return $reflector->newInstanceArgs($dependencies);
    }
}
