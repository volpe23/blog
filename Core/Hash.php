<?php

namespace Core;

class Hash
{

    public const CSRF_TOKEN_KEY = "csrf_token";

    public static function generateCsrf()
    {
        Session::set(static::CSRF_TOKEN_KEY, bin2hex(random_bytes(32)));
    }

    public static function getCsrfToken(): ?string
    {
        return Session::get(static::CSRF_TOKEN_KEY) ?? null;
    }

    public static function verifyCsrf(?string $token): bool
    {
        return hash_equals(Session::get(static::CSRF_TOKEN_KEY), $token);
    }
}
