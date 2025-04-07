<?php

namespace Core\Middleware;

use Core\App;
use Core\Hash;
use Core\Request;
use Core\Session;

class Csrf implements MiddlewareInterface
{
    public function handle()
    {
        $request = App::resolve(Request::class);
        $csrfTokenKey = Hash::CSRF_TOKEN_KEY;

        $inputToken = $request->$csrfTokenKey;
        if (!isset($inputToken) || empty(Hash::getCsrfToken()) || !Hash::verifyCsrf($request->$csrfTokenKey)) {
            http_response_code(403);
            die("CSRF token mismatch");
        }
    }

    public static function csrfInputField(): string
    {
        return "<input type='hidden' name='" . Hash::CSRF_TOKEN_KEY . "' value='" . htmlspecialchars(Hash::getCsrfToken()) . "'>";
    }
}
