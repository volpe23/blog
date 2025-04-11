<?php

namespace Core\Providers;

use Core\Auth;
use Core\ServiceProvider;

class AuthProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("auth", fn() => new Auth());
    }
}
