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

        if (!Auth::attempt([
            "username" => $username,
            "password" => $password
        ])) {
            return view("/login", [
                "usernameError" => "Username not correct",
                "passwordError" => "Password not correct"
            ]);
        }

        return redirect("/");
    }
    public function show()
    {
        // var_dump($_SERVER["HTTP_HOST"]);
        return view("user_register");
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            "username" => "required|min:4",
            "password" => "required|min:4"
        ]);

        $user = Users::create([
            "username" => $validated["username"],
            "password" => password_hash($validated["password"], PASSWORD_BCRYPT)
        ]);
        return redirect("login", [
            "successMessage" => "Register successful"
        ]);
    }

    public function logout()
    {
        Auth::logout();
        redirect("login");
    }
}
