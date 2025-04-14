<?php

use Core\App;
use Core\Facades\Facade;
use Core\Providers\AuthProvider;
use Core\Providers\ConfigProvider;
use Core\Providers\DatabaseProvider;
use Core\Providers\MiddlewareProvider;
use Core\Providers\RequestProvider;
use Core\Providers\RouterProvider;
use Core\Providers\SessionProvider;

require BASE_PATH . "config.php";


$app = (new App($config))->register([
    MiddlewareProvider::class,
    ConfigProvider::class,
    DatabaseProvider::class,
    RouterProvider::class,
    RequestProvider::class,
    AuthProvider::class,
    SessionProvider::class,
]);
$app->boot();

Facade::setFacadeApplication($app);

return $app;
