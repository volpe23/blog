<?php

namespace Core\Database\Support;

use ReflectionClass;
use ReflectionMethod;
use Core\Database\Database;



class QueryBuilder
{
    protected string $query;
    /**
     * @var array|null
     */
    protected array $columns;
    /**
     * @var array
     */
    protected array $binds = [
        "select" => [],
        "where" => [],
        "order" => [],
    ];

    /**
     * Where constraints for query
     * @var array<int, array<int, string>>
     */
    protected array $wheres = [];

    protected bool $distinct = false;
    public array $methods;

    protected $sqlBuilder;

    public function __construct(public string $table, private Database $connection)
    {
        $reflection = new ReflectionClass(static::class);
        $this->methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $this->sqlBuilder = new SqlBuilder($this);
    }

    public function select(array $columns = ["*"])
    {
        $this->binds["select"] = [];
        $this->columns = $columns;

        return $this;
    }

    /**
     * Adds where to the query
     * @param string $column
     * @param string|null $operator
     * @param string|null $value
     * 
     */
    public function where(string $column, ?string $operator = null, ?string $value = null): self
    {
        if (is_array($column)) {
            foreach ($column as $col) {
                $this->addWhere(...$col);
            }
        } else {
            $this->addWhere($column, $operator, $value);
        }

        return $this;
    }

    protected function addWhere(string $column, string $operator, string $value)
    {
        $this->binds["where"][] = $value;
        $this->wheres[] = [$column, $operator, $value];
    }

    /**
     * Sets the DISTINCT value for query
     * @param bool 
     * @return self
     */
    public function distinct($bool = true): self
    {
        $this->distinct = $bool;
        return $this;
    }

    /**
     * Insert into database
     * @param array $values
     * 
     * @return bool
     */
    public function insert(array $values): bool
    {
        $sql = $this->sqlBuilder->createInsertQuery($values);

        return $this->connection->insert($sql, $values);
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
