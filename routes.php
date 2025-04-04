<?php

use Core\Route;
use Core\Auth;
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
});
Route::get("/user_register", [UserController::class, "show"]);
Route::post("/user_register", [UserController::class, "store"]);
Route::get("/login", function () {
    return view("login");
});
Route::post("/login", [UserController::class, "login"]);
Route::post("/logout", [UserController::class, "logout"]);
