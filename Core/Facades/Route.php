<?php

namespace Core\Facades;

/**
 * @method static \Core\Routing\Route get(string $uri, array|callable $action)
 * @method static \Core\Routing\Route post(string $uri, array|callable $action)
 * @method static \Core\Routing\Route middleware(string $middleware)
 */
class Route extends Facade
{

    protected static function getFacadeAccessor(): string
    {
        return "router";
    }
}
