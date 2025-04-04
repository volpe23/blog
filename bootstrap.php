<?php

require BASE_PATH . "config.php";

use Core\App;
use Core\Auth;
use Core\Database;
use Core\Request;
use Core\Router;

$db = new Database($config["database"]);
App::bind(Database::class, fn() => $db);
App::singleton(Router::class, fn() => new Router());
App::singleton(Request::class, fn() => new Request($_GET, $_POST, $_SERVER, $_SESSION));
Auth::init($db, $config["user_table"]);
