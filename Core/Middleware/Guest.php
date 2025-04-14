<?php

namespace Core\Middleware;

use Core\Session;

class Guest implements MiddlewareInterface
{
    public function handle($app)
    {
        if ($app->session()->check("user")) {
            redirect("/");
        }
    }
}
