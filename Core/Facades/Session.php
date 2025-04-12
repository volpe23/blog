<?php

namespace Core\Facades;

class Session extends Facade {
    protected static function getFacadeAccessor(): string {
        return "session";
    }
}