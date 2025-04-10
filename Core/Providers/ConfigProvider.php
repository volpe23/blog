<?php

namespace Core\Providers;

use Core\Config;
use Core\ServiceProvider;

class ConfigProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton("config", fn() => new Config($this->app->getConfig()));
    }
}
