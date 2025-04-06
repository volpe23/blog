<?php

namespace Migrations;

abstract class Field
{
    protected string $schemaText;
    protected string $fieldName;
    protected string $def = "";
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

    public function default(string $val): static
    {
        // $this->schemaText .= " DEFAULT {$val}";
        $this->def = "DEFAULT " . $val;
        return $this;
    }

    public function getSchemaText(): string
    {
        return trim($this->fieldName . " " . $this->schemaText . " " . $this->def);
    }

    public function getFieldName(): string
    {
        return $this->fieldName;
    }

    public static $PRIMARY_KEY = " PRIMARY KEY";
    public static $NOT_NULL = " NOT NULL";
    public static $UNIQUE = " UNIQUE";
}
