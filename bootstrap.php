<?php

use Core\App;
use Core\Facades\Facade;
use Core\Providers\ConfigProvider;
use Core\Providers\DatabaseProvider;
use Core\Providers\MiddlewareProvider;
use Core\Providers\RequestProvider;
use Core\Providers\RouterProvider;

require BASE_PATH . "config.php";


$app = (new App($config))->register([
    MiddlewareProvider::class,
    ConfigProvider::class,
    DatabaseProvider::class,
    RouterProvider::class,
    RequestProvider::class,
]);

Facade::setFacadeApplication($app);

return $app;
