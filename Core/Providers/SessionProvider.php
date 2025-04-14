<?php

namespace Core\Providers;

use Core\ServiceProvider;
use Core\Session;

class SessionProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("session", fn() => new Session());
    }
}
