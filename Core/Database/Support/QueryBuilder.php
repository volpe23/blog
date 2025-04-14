<?php

namespace Core\Database\Support;

use ReflectionClass;
use ReflectionMethod;
use Core\Database\Database;
use Core\Model;



class QueryBuilder
{
    protected string $query;
    /**
     * @var array|null
     */
    public array $columns;
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
    public array $wheres = [];

    public bool $distinct = false;
    public array $methods;

    protected $sqlBuilder;

    /**
     * @var Model $model
     */
    protected $model;

    public function __construct(public string $table, private Database $connection)
    {
        $reflection = new ReflectionClass(static::class);
        $this->methods = $reflection->getMethods(ReflectionMethod::IS_PUBLIC);
        $this->sqlBuilder = new SqlBuilder;
    }

    /**
     * Adds columns to select clause
     * @return $this
     */
    public function select(array $columns = ["*"]): self
    {
        $this->binds["select"] = [];
        $this->columns = $columns;

        return $this;
    }

    /**
     * Adds where to the query
     * @param string|array $column
     * @param string|null $operator
     * @param string|null $value
     * 
     * @return $this
     */
    public function where(string|array $column, ?string $operator = null, ?string $value = null): self
    {
        if (is_array($column)) {
            foreach ($column as $col) {
                [$column, $operator, $value] = $col;
                $this->addWhere($column, $operator, $value);
            }
        } else {
            $this->addWhere($column, $operator, $value);
        }

        return $this;
    }

    /**
     * Add a single where
     * @param string|array $column
     * @param string|null $operator
     * @param string|null $value
     * @param string $boolean
     * 
     * @return void
     */
    protected function addWhere(string $column, string $operator, string $value, string $boolean = "and")
    {
        $this->binds["where"][] = $value;
        $this->wheres[] = compact("column", "operator", "value", "boolean");
    }

    /**
     * Sets the DISTINCT value for query
     * @param bool 
     * 
     * @return self
     */
    public function distinct($bool = true): self
    {
        $this->distinct = $bool;
        return $this;
    }

    /**
     * Returns all the results from db based on built query
     * @return array
     */
    public function get(): array
    {
        $sql = $this->sqlBuilder->createSelectQuery($this);

        return $this->connection->query($sql, $this->getFlatBindings())->fetchAllClass($this->model::class);
    }

    public function first(): Model
    {
        return $this->connection->query($this->sqlBuilder->createSelectQuery($this), $this->getFlatBindings())->fetchClass($this->model::class);
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

    /**
     * Returns all the bindings in a single array
     * @return array
     */
    protected function getFlatBindings(): array
    {
        return array_reduce($this->binds, fn($res, $curr) => array_merge($res, $curr), []);
    }

    /**
     * @param Model $model;
     * 
     * @return $this
     */
    public function setModel(Model $model): self
    {
        $this->model = $model;
        return $this;
    }

    // /**
    //  * Retrieves and builds the sql query
    //  * @return string
    //  */
    // public function getSqlQuery(): string
    // {
    // return $this->sqlBuilder->createSelectQuery($this);
    // }
}
