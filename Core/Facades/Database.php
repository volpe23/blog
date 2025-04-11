<?php

namespace Core\Facades;

class Database extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return "database";
    }
}
