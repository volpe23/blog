<?php

namespace Core\Database\Support;

class InstertQuery extends QueryBuilder
{

    public function __construct($table, array $fields)
    {
        $this->binds = $fields;
        $this->query = "INSERT INTO $table ";
    }

    protected function buildFieldNames(): string
    {
        return "(" . implode(", ", array_keys($this->binds)) . ") ";
    }

    protected function buildValues(): string
    {
        return "(" . implode(", ", array_map(fn($bind) => ":$bind", $this->binds)) . ")";
    }

    public function __toString()
    {
        return $this->query . $this->buildFieldNames() . "VALUES " . $this->buildValues();
    }
}
