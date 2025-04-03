<?php

namespace Core;

use Core\Database;
use Core\Model;

class Auth
{
    /**
     * @param class-string<Model> $usersTable
     */
    private static string $usersTable;
    private static Database $db;
    // Create a class that stores info about current user
    public static function check(string $username, string $password): bool
    {
        return false;
    }

    /**
     * @param array{username: string, password: string} $credentials
     */
    public static function attempt(array $credentials): bool
    {
        if (!isset($credentials["username"], $credentials["password"])) {
            return false;
        }
        self::$usersTable::get($credentials);

        return true;
    }

    public static function init(Database $db, string $tableName): void
    {
        self::$usersTable = $tableName;
        self::$db = $db;
    }
}
