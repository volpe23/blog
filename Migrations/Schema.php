<?php

namespace Migrations;

require "./Core/Database.php";

use Core\Database;

class Schema
{
    public static Database $db;
    public static function create(string $tableName, callable $cb)

    {
        $table = new Blueprint($tableName);
        $cb($table);

        $sqlite_master = static::tableExists($tableName);
        $query = $table->createTableStr();

        if ($sqlite_master) {
            if ($query === $sqlite_master["sql"]) return;
            if ($tableName !== $sqlite_master["name"]) {
                static::changeTableName($tableName, $table);
                return;
            };
            $tableDetails = static::getTableDetails($tableName);
            $currTableCols = sort(array_reduce($tableDetails, function ($arr, $curr) {
                $arr[] = $curr["name"];
                return $arr;
            }, []));
            if ($table->cols() == $currTableCols) {
            }
        } else {
            static::$db->query($query);
        }
    }

    private static function changeTableName(string $newName, Blueprint $bp)
    {
        static::$db->query("ALTER TABLE {$bp->tableName} TO {$newName}");
    }

    private static function getTableDetails(string $tableName): array
    {
        return static::$db->query("PRAGMA table_info{$tableName}")->fetchAll();
    }

    private static function tableExists(string $tableName): bool
    {
        return static::$db->query("SELECT name FROM sqlite_master WHERE type='table' AND name=:table", ["table" => $tableName])->fetch();
    }
}
