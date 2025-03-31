<?php
use Core\App;
use Core\Router;

const BASE_PATH = __DIR__ . "/../";
require BASE_PATH . "vendor/autoload.php";
require BASE_PATH . "bootstrap.php";
require BASE_PATH . "routes.php";
require BASE_PATH . "/views/partials/head.php";
require BASE_PATH . "Core/functions.php";


session_start();
$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$method = $_SERVER["REQUEST_METHOD"];

App::resolve(Router::class)->route($uri, $method);


require __DIR__ . "/../views/partials/foot.php";