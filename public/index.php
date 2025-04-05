<?php

use Core\App;
use Core\Router;
use Core\Session;

const BASE_PATH = __DIR__ . "/../";

session_start();

require BASE_PATH . "vendor/autoload.php";
require BASE_PATH . "bootstrap.php";
require BASE_PATH . "routes.php";
require BASE_PATH . "/views/partials/head.php";
require BASE_PATH . "Core/functions.php";


$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$method = $_SERVER["REQUEST_METHOD"];

App::resolve(Router::class)->route($uri, $method);
Session::unflash();


require __DIR__ . "/../views/partials/foot.php";
