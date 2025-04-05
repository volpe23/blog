<?php

namespace Core\Middleware;
use Core\Session;

class Authenticated implements MiddlewareInterface {
    public function handle() {
        if (!Session::check("user")) {
            redirect("/login");
        }
    }
}