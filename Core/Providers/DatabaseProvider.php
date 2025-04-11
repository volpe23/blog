<?php

namespace Core\Providers;

use Core\ServiceProvider;
use Core\Database\Database;

class DatabaseProvider extends ServiceProvider
{
    /**
     * Register the database binding
     * 
     * @return void
     */
    public function register()
    {
        $this->app->singleton("db", fn() => new Database($this->app->getConfigValue("database"), $this->app));
    }

    /**
     * Bootstrap database 
     * 
     * @return void
     */
    public function boot() {

    }
}
