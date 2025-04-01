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
    return view("index");
});
Route::get("/user_register", [UserController::class, "show"]);
Route::post("/user_register", [UserController::class, "store"]);
Route::get("/login", function () {
    return view("login");
});

