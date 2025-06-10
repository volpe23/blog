<?php

namespace Core\Facades;

/**
 * @method static \Core\Routing\Route get(string $uri, array|callable $action)
 * @method static \Core\Routing\Route post(string $uri, array|callable $action)
 * @method static \Core\Routing\Route middleware(string $middleware)
 * @method static \Core\Routing\Route route($uri, string $method)
 */
class Route extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return "router";
    }
}
