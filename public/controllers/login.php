<?php

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    require base_path("views/login.view.php");
} else if ($method === "POST") {

}