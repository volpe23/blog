<?php

use Core\Route;
use Controllers\UserController;

$navRoutes = [
    "/" => [
        "name" => "Home",
        "controller" => "public/controllers/home.php"
    ],
    "/hello" => [
        "name" => "Hello",
        "controller" => "public/controllers/hello.php"
    ],
    "/user" => [
        "name" => "User",
        "controller" => "public/controllers/user.php"
    ],
    "/user_register" => [
        "name" => "User Register",
        "controller" => "public/controllers/user_register.php"
    ],
    "/login" => [
        "name" => "Login",
        "controller" => "public/controllers/login.php"
    ]
];

Route::get("/", function () {
    return view("views/index.view.php");
});
Route::get("/user_register", function () {
    require base_path("public/controllers/user_register.php");
});
Route::post("/user_register", [UserController::class, "register"]);

