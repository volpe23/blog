<?php
const BASE_PATH = __DIR__ . "/../";
require BASE_PATH . "vendor/autoload.php";
require BASE_PATH . "routes.php";
require BASE_PATH . "/views/partials/head.php";
require BASE_PATH . "bootstrap.php";
require BASE_PATH . "Core/functions.php";

session_start();
?>


<?php require __DIR__ . "/../views/partials/foot.php"; ?>