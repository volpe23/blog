<?php

namespace Core\Providers;

use Core\Database;
use Core\ServiceProvider;

class DatabaseProvider extends ServiceProvider
{
    /**
     * Register the database binding
     * 
     * @return void
     */
    public function register()
    {
        $this->app->singleton("db", fn() => new Database($this->app->get("config")["database"], $this->app));
    }

    /**
     * Bootstrap database 
     * 
     * @return void
     */
    public function boot() {

    }
}
