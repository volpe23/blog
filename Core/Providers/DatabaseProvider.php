<?php

namespace Core\Providers;

use Core\Database;
use Core\ServiceProvider;

class DatabaseProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind("database", fn() => new Database($this->app->get("config")["database"]));
    }
}
