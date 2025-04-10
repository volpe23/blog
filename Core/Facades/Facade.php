<?php

namespace Core\Facades;

use Core\App;

abstract class Facade
{
    /**
     * @var \Core\App
     */
    protected static $app;

    /**
     * @param \Core\App $app
     * 
     * @return void
     */
    public static function setFacadeApplication(App $app) {
        static::$app = $app;
    }

    /**
     * @return \Core\App
     */
    public static function getFacadeApplication() {
        return static::$app;
    }
}
