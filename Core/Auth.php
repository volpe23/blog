<?php

namespace Core;

use Core\Database;
use Core\Model;

class Auth
{
    /**
     * @param class-string<Model> $usersModel
     */
    private static string $usersModel;
    public static ?Model $user;
    private static Database $db;
    // Create a class that stores info about current user
    public static function check(): bool
    {
        return (bool) self::$user;
    }

    /**
     * @param array{username: string, password: string} $credentials
     */
    public static function attempt(array $credentials): bool
    {
        if (!isset($credentials["username"], $credentials["password"])) {
            return false;
        }
        $user = self::$usersModel::get([
            "username" => $credentials["username"]
        ]);

        if ($user) {
            if (password_verify($credentials["password"], $user->password)) {
                self::$user = $user;
                self::login($user);
                return true;
            };
            echo "not ok usert";
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

    public static function logout() {
        Session::destroy();
    }

    public static function init(Database $db, string $tableName): void
    {
        self::$usersModel = $tableName;
        self::$user = null;
        if (Session::check("user")) {
            self::$user = self::$usersModel::get([
                "username" => Session::get("user")["username"]
            ]);
        }
        self::$db = $db;
    }
}
