<?php

namespace Core\Facades;

/**
 * @method static bool attempt(Auth)
 * @method static login(Auth)
 * @method static logout(Auth)
 */
class Auth extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "auth";
    }
}
