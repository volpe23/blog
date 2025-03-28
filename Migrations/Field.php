<?php

namespace Migrations;

abstract class Field
{
    protected string $schemaText;
    protected string $fieldName;
    public function __construct($fieldName)
    {
        $this->fieldName = $fieldName;
    }
    public function nullable(bool $null): static
    {
        $this->schemaText .= $null ? "" : static::$NOT_NULL;
        return $this;
    }

    public function unique(): static
    {
        $this->schemaText .= static::$UNIQUE;
        return $this;
    }

    public function primaryKey(): static
    {
        $this->schemaText .= static::$PRIMARY_KEY;
        return $this;
    }

    public function getSchemaText(): string
    {
        return $this->fieldName . " " . $this->schemaText;
    }

    public static $PRIMARY_KEY = " PRIMARY KEY";
    public static $NOT_NULL = " NOT NULL";
    public static $UNIQUE = " UNIQUE";

    // public static function nullable()
    // foreach($options as $option => $value) {
    // switch ($option) {
    // case "nullable":
    // $fieldString .= $value ? "" : "NOT NULL";
    // break;
    // }
    // }
}
