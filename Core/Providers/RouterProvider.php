<?php

namespace Core\Providers;

use Core\Routing\Router;
use Core\ServiceProvider;

class RouterProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("router", fn() => new Router($this->app));
    }
}
