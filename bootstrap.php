<?php

use Core\App;
use Core\Providers\ConfigProvider;
use Core\Providers\DatabaseProvider;
use Core\Providers\RouterProvider;

require BASE_PATH . "config.php";


$app = new App($config);
$app->register([
    ConfigProvider::class,
    DatabaseProvider::class,
    RouterProvider::class
]);
