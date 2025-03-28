<?php

namespace Migrations;
require "./Core/Database.php";
use Core\Database;

class Schema
{
    public static Database $db;

    public static function create(string $tableName, $cb)
    {
        $table = new Blueprint($tableName);
        $cb($table);
        $query = $table->createTableStr();
        static::$db->query($query);
    }

    private static function tableExists(string $tableName): bool {
        return static::$db->query("SELECT name FROM sqlite_master WHERE type='table' AND name=:table", ["table" => $tableName])->fetch();
    }
}
