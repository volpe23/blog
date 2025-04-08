<?php

namespace Core\Providers;

use Core\Config;
use Core\ServiceProvider;

class ConfigProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind("config", fn() => new Config($this->app->getConfig()));
    }
}
