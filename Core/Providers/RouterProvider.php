<?php

namespace Core\Providers;

use Core\Router;
use Core\ServiceProvider;

class RouterProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("router", fn() => new Router);
    }
}
