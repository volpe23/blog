<?php

namespace Core\Facades;

/**
 * @method \Core\Database\Database query(string $query, array $attrs)
 */
class Database extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return "db";
    }
}
