<?php

namespace Core\Providers;

use Core\Auth;
use Core\ServiceProvider;

class AuthProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("auth", fn($app) => new Auth($app));
    }

    public function boot()
    {
        // TODO: load the user model class from config
        $userModel = $this->app->get("config")["user_model"];
        $this->app->get("auth")->setUserModel($userModel);
    }
}
