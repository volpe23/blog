<?php

namespace Core\Middleware;

use Core\Session;

class Authenticated implements MiddlewareInterface
{
    public function handle($app)
    {
        if (!$app->session()->check("user")) {
            redirect("/login");
        }
    }
}
