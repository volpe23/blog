<?php

namespace Core\Middleware;
use Core\Session;

class Guest implements MiddlewareInterface {
    public function handle() {
        if (Session::check("user")) {
            redirect("/");
        }
    }
}