<?php

namespace Migrations;

use Core\Database;

class Schema
{
    public static Database $db;
    public static function create(string $tableName, callable $cb)

    {
        $table = new Blueprint($tableName);
        $cb($table);

        $sqlite_master = static::tableExists($tableName);
        $tableDetails = static::getTableDetails($tableName);
        $query = $table->createTableStr();

        if (is_array($sqlite_master)) {
            if ($query === $sqlite_master["sql"]) return;
            if ($tableName !== $sqlite_master["name"]) {
                static::changeTableName($tableName, $table);
                return;
            };
            $currTableCols = array_reduce($tableDetails, function ($arr, $curr) {
                $arr[] = $curr["name"];
                return $arr;
            }, []);
            sort($currTableCols);
            $diff = array_diff($table->cols(), $currTableCols);

            if (count($diff) > 0) {
                $diffFields = array_filter($table->fields, fn($f) => in_array($f->getFieldName(), $diff));
                foreach ($diffFields as $df) {
                    static::addColumn($tableName, $df);
                }
            }
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

    private static function addColumn(string $tableName, Field $newCol)
    {
        static::$db->query("ALTER TABLE {$tableName} ADD COLUMN {$newCol->getSchemaText()}");
    }

    private static function getTableDetails(string $tableName): array
    {
        return static::$db->query("PRAGMA table_info({$tableName})")->fetchAll();
    }

    private static function tableExists(string $tableName): bool | array
    {
        return static::$db->query("SELECT * FROM sqlite_master WHERE type=:type AND name=:table", ["table" => $tableName, "type" => "table"])->fetch();
    }
}
