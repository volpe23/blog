<?php

namespace Core;

class Session
{

    public static function check($key): bool
    {
        return isset($_SESSION[$key]);
    }
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }

    public static function flash(string $key,string $value) {
        $_SESSION["_flash"][$key] = $value;
    }

    public static function destroy()
    {
        $_SESSION = [];
        session_destroy();
        $params = session_get_cookie_params();
        setcookie('PHPSESSID', '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
    }

    public static function get($key)
    {
        return $_SESSION[$key] ?? null;
    }
}
