<?php

namespace Core\Facades;

use Core\App;
use Exception;
use RuntimeException;

abstract class Facade
{
    /**
     * @var \Core\App
     */
    protected static $app;

    /**
     * Resolved app container instances
     */
    protected static $resolvedInstance;

    /**
     * @param \Core\App $app
     * 
     * @return void
     */
    public static function setFacadeApplication(App $app)
    {
        static::$app = $app;
    }

    /**
     * @return \Core\App
     */
    public static function getFacadeApplication()
    {
        return static::$app;
    }

    /**
     * @param string $name
     */
    protected static function resolveFacadeInstance($name)
    {
        if (isset(static::$resolvedInstance[$name])) return static::$resolvedInstance[$name];

        if (static::$app) {
            // dd(static::$app);
            return static::$resolvedInstance[$name] = static::$app[$name];
        }
    }

    protected static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    abstract protected static function getFacadeAccessor(): string;

    public static function __callStatic($method, $arguments)
    {
        // Implement the aliasing to app instances
        $instance = static::getFacadeRoot();

        if (!$instance) {
            throw new RuntimeException("a facade root has not been set");
        }

        return $instance->$method(...$arguments);
    }
}
