<?php

use Core\Models\Users;

$method = $_SERVER["REQUEST_METHOD"];

if ($method === "GET") {
    require base_path("views/user_register.view.php");
} else if ($method === "POST") {
    $flag = true;
    $usernameError = $passwordError = "";

    $username = $_POST["username"];
    $password = $_POST["password"];

    if (empty($_POST["username"]) || !isset($_POST["username"])) {
        $usernameError = "Please provide an username";
        $flag = false;
    }
    if (empty($_POST["password"]) || !isset($_POST["password"])) {
        $passwordError = "Please provide a password";
        $flag = false;
    }

    if (!$flag) {
        require base_path("views/user_register.view.php");
        return;
    }

    $user = Users::create([
        "username" => $username,
        "password" => password_hash($password, PASSWORD_BCRYPT)
    ]);
    $user->save();

    dd($user);
}
