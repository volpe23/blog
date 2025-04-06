<?php

namespace Core;

use ErrorException;
use ReflectionClass;
use ReflectionMethod;

enum OrderingDirection: string
{
    case Asc = "ASC";
    case Dsc = "DSC";
}

class QueryBuilder
{
    private string $query;
    private array $binds = [];
    public array $methods;

    public function __construct(private string $table)
    {
        $reflection = new ReflectionClass(static::class);
        $this->methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
    }

    public function select(array $cols = ["*"]): static
    {
        $this->query = "SELECT " . implode(", ", $cols) . " FROM $this->table";
        return $this;
    }

    public function distinct(): static
    {
        if (!str_contains($this->query, "SELECT")) {
            $this->query = "SELECT DISTINCT";
        } else {
            $pos = strlen("SELECT");
            $this->query = substr_replace($this->query, " DISTINCT ", $pos);
        }
        return $this;
    }

    public function where(string | array $col, mixed $value, string $comp = "="): static
    {
        $this->query .= str_contains($this->query, "WHERE") ? " AND " : " WHERE ";
        if (is_array($col)) {
            if (count($col) !== 3) throw new ErrorException("wrong number of arguments provided in where() array");
            $conditions = array_map(function ($cnd) {
                [$c, $comp, $value] = $cnd;
                $this->binds[$c] = $value;
                return "$c" . $comp . ":$c";
            }, $col);
            $this->query .= implode(" AND ", $conditions);
        } else {
            $condition = "$col" . $comp . ":$col";
            $this->query .= $condition;
            $this->binds[$col] = $value;
        }
        return $this;
    }

    public function orWhere(string | callable $col, string $value, string $comp = "="): static
    {
        $this->query .= str_contains($this->query, "WHERE") ? " AND " : " WHERE ";
        if (is_callable($col)) {
            $this->query .= "(";
            $col($this);
            $this->query .= ")";
        } else {
            $condition = "$col" . $comp . ":$col";
            $this->query .= $condition;
            $this->binds[$col] = $value;
        }
        return $this;
    }

    public function orderBy(string $col, OrderingDirection $order = OrderingDirection::Asc): static
    {
        $this->query .= !str_contains($this->query, "ORDER BY") ? " ORDER BY $col $order" : ", $col $order";
        return $this;
    }

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getBinds(): array {
        return $this->binds;
    }

    public static function table(string $tableName): static
    {
        return new static($tableName);
    }
}
