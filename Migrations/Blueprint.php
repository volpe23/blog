<?php

namespace Migrations;

require "./Migrations//Fields.php";

use Migrations\IntField;
use Migrations\StringField;

class Blueprint
{
    /** @var Field[] */
    public array $fields = [];
    public string $tableName;
    public function __construct(string $tableName)
    {
        $this->tableName = $tableName;
    }

    public function __set($name, $value)
    {
        $this->fields[$name] = $value;
    }

    public function string(string $fieldName, int $length = 255): Field
    {
        $field = new StringField($fieldName, $length);
        $this->fields[] = $field;
        return $field;
    }

    public function int(string $fieldName): Field
    {
        $field = new IntField($fieldName);
        $this->fields[] = $field;
        return $field;
    }

    public function id(string $fieldType = "int", string $fieldName = "id"): Field
    {
        $field = new IntField($fieldName);
        switch ($fieldType) {
            case "int":
                break;
        }
        $this->fields[] = $field;

        return $field->primaryKey();
    }

    public function datetime(string $fieldName): Field {
        $field = new DateField($fieldName);
        $this->fields[] = $field;
        return $field;
    }

    /** @var string[] */
    public function cols()
    {
        $arr = array_reduce($this->fields, function ($arr, $curr) {
            $arr[] = $curr->getFieldName();
            return $arr;
        }, []);
        sort($arr);
        return $arr;
    }

    public function createTableStr(): string
    {
        $str = "CREATE TABLE IF NOT EXISTS  {$this->tableName} (";
        for ($i = 0; $i < count($this->fields); $i++) {
            $str .= $this->fields[$i]->getSchemaText();
            if ($i !== count($this->fields) - 1) {
                $str .= ", ";
            }
        }

        return $str . ");";
    }
}
