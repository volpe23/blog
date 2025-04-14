<?php

namespace Core\Providers;

use Core\ServiceProvider;
use Core\Session;

class SessionProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind("session", fn() => new Session($_SESSION));
    }
}
