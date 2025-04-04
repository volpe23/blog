<?php

use Core\Route;
use Core\Auth;
use Core\Middleware;
use Controllers\UserController;

$navRoutes = [
    "/" => [
        "name" => "Home",
        "restriction" => Auth::check()
    ],
    "/user" => [
        "name" => "User",
        "restriction" => Auth::check()
    ],
    "/user_register" => [
        "name" => "User Register",
        "restriction" => !Auth::check()
    ],
    "/login" => [
        "name" => "Login",
        "restriction" => !Auth::check()
    ],
    
];

Route::get("/", function () {
    return view("index");
})->middleware("auth");
Route::get("/user_register", [UserController::class, "show"])->middleware("guest");
Route::post("/user_register", [UserController::class, "store"])->middleware("guest");
Route::get("/login", function () {
    return view("login");
})->middleware("guest");
Route::post("/login", [UserController::class, "login"])->middleware("guest");
Route::post("/logout", [UserController::class, "logout"]);
Route::get("/user", fn() => view("user"))->middleware("auth");
