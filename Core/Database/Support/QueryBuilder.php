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

    public bool $distinct = false;
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

    protected function addWhere(string $column, string $operator, string $value, string $boolean = "and")
    {
        $this->binds["where"][] = $value;
        $this->wheres[] = compact($column, $operator, $value, $boolean);
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

    public function get()
    {
        $sql = $this->sqlBuilder->createSelectQuery($this);

        $this->connection->query($sql, $this->getFlatBindings())->fetchAll();
    }

    /**
     * Insert into database
     * @param array $values
     * 
     * @return bool
     */
    public function insert(array $values): bool
    {
        $sql = $this->sqlBuilder->createInsertQuery($this, $values);

        return $this->connection->insert($sql, $values);
    }

    protected function getFlatBindings(): array
    {
        return array_reduce($this->binds, fn($res, $curr) => array_merge($res, $curr));
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
