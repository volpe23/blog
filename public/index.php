<?php

use Core\Facades\Route;

const BASE_PATH = __DIR__ . "/../";

session_start();

require BASE_PATH . "vendor/autoload.php";
require BASE_PATH . "bootstrap.php";
require BASE_PATH . "Core/functions.php";
require BASE_PATH . "routes.php";
require BASE_PATH . "/views/partials/head.php";


$uri = parse_url($_SERVER["REQUEST_URI"])["path"];
$method = $_SERVER["REQUEST_METHOD"];

Route::route($uri, $method);
session()->unflash();


require __DIR__ . "/../views/partials/foot.php";
