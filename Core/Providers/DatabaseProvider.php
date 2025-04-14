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
        $this->app->singleton("db", fn($app) => new Database($app->getConfigValue("database"), $app));
    }

    /**
     * Bootstrap database 
     * 
     * @return void
     */
    public function boot() {}
}
