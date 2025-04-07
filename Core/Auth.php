<?php

namespace Core;

use Core\Database;
use Core\Model;
use Core\Models\Users;

class Auth
{
    /**
     * @param class-string<Model> $usersModel
     */
    private static string $usersModel;
    public static ?Model $user;
    // Create a class that stores info about current user
    public static function check(): bool
    {
        return Session::check("user");
    }

    /**
     * @param array{username: string, password: string} $credentials
     */
    public static function attempt(array $credentials): bool
    {
        if (!isset($credentials["username"], $credentials["password"])) {
            return false;
        }
        $user = self::$usersModel::where([
            [
                "username",
                "=",
                $credentials["username"]
            ]
        ])->first();

        if ($user) {
            if (password_verify($credentials["password"], $user->password)) {
                self::$user = $user;
                self::login($user);
                return true;
            };
        }

        return false;
    }

    public static function login($user)
    {
        Session::set("user", [
            "username" => $user->username
        ]);
        session_regenerate_id(true);
    }

    public static function logout()
    {
        Session::destroy();
    }

    public static function user(): ?Users
    {
        $res = Session::check("user") ? self::$usersModel::where("username", Session::get("user")["username"])->first() : null;
        return $res;
    }

    public static function init($usersModel)
    {
        static::$usersModel = $usersModel;
    }
}
