<?php

use Controllers\PostsController;
use Core\Facades\Route;
use Core\Facades\Auth;
use Controllers\UserController;

$navRoutes = [
    "/" => [
        "name" => "Home",
        "restriction" => Auth::check()
    ],
    "/posts" => [
        "name" => "Posts",
        "restriction" => true
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

Route::middleware("guest")->group(function () {
    Route::get("/user_register", [UserController::class, "show"]);
    Route::post("/user_register", [UserController::class, "store"]);

    Route::get("/user", fn() => view("user"));

    Route::get("/login", function () {
        return view("login");
    });
    Route::post("/login", [UserController::class, "login"]);
});

Route::post("/logout", [UserController::class, "logout"])->csrfExempt(true);

Route::prefix("/posts")->group(function () {
    Route::get("/", [PostsController::class, "index"]);

    Route::middleware("auth")->group(function () {
        Route::post("/create", [PostsController::class, "store"]);
        Route::get("/{id}", [PostsController::class, "show"]);
    });
});
