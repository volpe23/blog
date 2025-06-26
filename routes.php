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
Route::get("/user_register", [UserController::class, "show"])->middleware("guest");
Route::post("/user_register", [UserController::class, "store"])->middleware("guest");
Route::get("/login", function () {
    return view("login");
})->middleware("guest");
Route::post("/login", [UserController::class, "login"])->middleware("guest");
Route::post("/logout", [UserController::class, "logout"])->csrfExempt(true);
Route::get("/user", fn() => view("user"))->middleware("auth");

// Routes for posts
// Route::get("/posts", [PostsController::class, "index"]);
// Route::post("/posts_create", [PostsController::class, "store"])->middleware("auth");
// Route::get("/posts/{id}", [PostsController::class, "show"])->middleware("auth");
Route::prefix("/posts")->group(function () {
    Route::get("/", [PostsController::class, "index"]);
    Route::post("/create", [PostsController::class, "store"])->middleware("auth");
    Route::get("/{id}", [PostsController::class, "show"])->middleware("auth");
});
