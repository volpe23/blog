<?php

namespace Core;

class Session
{

    public static function check($key): bool {
        return isset($_SESSION[$key]);
    }
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function get($key)
    {
        return $_SESSION[$key] ?? null;
    }
}
