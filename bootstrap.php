<?php

require BASE_PATH . "config.php";

use Core\App;
use Core\Database;
use Core\Router;

App::singleton(Database::class, fn() => new Database($config["database"]));
App::singleton(Router::class, fn() => new Router());
