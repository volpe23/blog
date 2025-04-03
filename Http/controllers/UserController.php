<?php

namespace Controllers;

use Core\Models\Users;
use Core\Request;
use Core\Auth;

class UserController
{
    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        Auth::attempt([
            "username" => $username,
            "password" => $password
        ]);
    }
    public function show()
    {
        // var_dump($_SERVER["HTTP_HOST"]);
        return view("user_register");
    }

    public function store()
    {

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
            return view("user_register", [
                "usernameError" => $usernameError,
                "passwordError" => $passwordError
            ]);
        }

        $user = Users::create([
            "username" => $username,
            "password" => password_hash($password, PASSWORD_BCRYPT)
        ]);
        $user->save();
        return redirect("login", [
            "successMessage" => "Register successful"
        ]);
    }
}
