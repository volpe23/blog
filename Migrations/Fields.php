<?php

namespace Migrations;

use Migrations\Field;

class StringField extends Field
{
    protected string $schemaText = "VARCHAR";
    public function __construct(string $fieldName, int $length = 255)
    {
        parent::__construct($fieldName);
        $this->schemaText .= "({$length})";
    }
}

class IntField extends Field
{
    protected string $schemaText = "INTEGER";
    public function __construct(string $fieldName)
    {
        parent::__construct($fieldName);
    }
}

class DateField extends Field
{
    protected string $schemaText = "TEXT";
    const CURRENT_TIMESTAMP = " CURRENT_TIMESTAMP";
    public function __construct(string $fieldName)
    {
        parent::__construct($fieldName);
        $this->default(self::CURRENT_TIMESTAMP);
    }
}

class ForeignKeyField extends Field
{
    protected string $schemaText = "FOREIGN KEY";
    public function __construct(string $fkFieldName)
    {
        $this->schemaText .= " ($fkFieldName)";
    }

    public function references($referencedTable, string $row = ""): static
    {
        $this->schemaText .= " REFERENCES $referencedTable";
        $this->schemaText .= !empty($row) ? " ($row)" : "";
        return $this;
    }

    public function getSchemaText(): string
    {
        return $this->schemaText;
    }
}
