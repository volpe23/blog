<?php

namespace Migrations;

require "./Migrations/Field.php";

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
    public static $CURRENT_TIMESTAMP = " CURRENT_TIMESTAMP";
    public function __construct(string $fieldName)
    {
        parent::__construct($fieldName);
    }

}
