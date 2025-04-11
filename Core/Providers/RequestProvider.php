<?php

namespace Core\Providers;

use Core\Request;
use Core\ServiceProvider;

class RequestProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(Request::class, fn() => new Request($_GET, $_POST, $_SERVER, $_SESSION));
    }
}
