<?php

namespace Core\Middleware;
use Core\Session;

class Authenticated {
    public static function handle() {
        if (!Session::check("user")) {
            redirect("/login");
        }
    }
}