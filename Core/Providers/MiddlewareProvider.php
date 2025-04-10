<?php

namespace Core\Providers;

use Core\Middleware;
use Core\ServiceProvider;

class MiddlewareProvider extends ServiceProvider {
    public function register()
    {
        $this->app->singleton("middleware", fn() => new Middleware());
    }
}