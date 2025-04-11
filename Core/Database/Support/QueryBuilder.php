<?php

namespace Core\Database\Support;

use ReflectionClass;
use ReflectionMethod;
use Core\Database\Database;



abstract class QueryBuilder
{
    protected string $query;
    protected array $binds = [];
    public array $methods;

    public function __construct(private string $table, private Database $connection)
    {
        $reflection = new ReflectionClass(static::class);
        $this->methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
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

    public function getQuery(): string
    {
        return $this->query;
    }

    public function getBinds(): array
    {
        return $this->binds;
    }
}
