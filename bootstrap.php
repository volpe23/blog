<?php

require BASE_PATH . "config.php";

use Core\App;
use Core\Auth;
use Core\Config;
use Core\Container;
use Core\Database;
use Core\Request;
use Core\Router;

App::init(new Container());
Auth::init($config["user_table"]);

App::bind(Config::class, fn() => new Config($config));
App::bind(Database::class, fn() => new Database($config["database"]));
App::singleton(Router::class, fn() => new Router());
App::singleton(Request::class, fn() => new Request($_GET, $_POST, $_SERVER, $_SESSION));
