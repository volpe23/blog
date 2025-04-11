<?php

namespace Core\Database;

use PDO;
use PDOStatement;
use Core\Container;
use Core\Database\Support\QueryBuilder;

class Database
{
    private PDO $conn;
    private PDOStatement $statement;

    /**
     * App container
     * @var \Core\Container
     */
    protected $container;

    public function __construct(private string $file, Container $container)
    {
        $this->conn = new PDO("sqlite:{$file}", null, null, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        $this->container = $container;
    }

    public function insert(string $query, array $attrs): bool
    {
        $this->statement = $this->conn->prepare($query, $attrs);
        return $this->statement->execute();
    }

    /**
     * Queries the database with sql statement and attributes
     * @param string $query
     * @param array $attrs
     * 
     * @return self
     */
    public function query(string $query, array $attrs = []): Database
    {
        $this->statement = $this->conn->prepare($query);
        if (!$this->statement->execute($attrs)) echo "Query failed";

        return $this;
    }

    /**
     * @return array
     */
    public function fetchAll(): array
    {
        return $this->statement->fetchAll();
    }

    public function fetchAllClass(?string $class, ?array $constructorArgs = null): array
    {
        return $this->statement->fetchAll(PDO::FETCH_CLASS, $class, $constructorArgs);
    }

    /**
     * @return array|bool
     */
    public function fetch(): mixed
    {
        return $this->statement->fetch();
    }

    public function fetchClass(?string $class, ?array $constructorArgs = []): object | false
    {
        return $this->statement->fetchObject($class, $constructorArgs);
    }

    /**
     * Checks wether exists in the table
     * @param string $query
     * @param array $attrs
     * 
     * @return bool
     */
    public function check(string $query, array $attrs = []): bool
    {
        return $this->query($query, $attrs)->statement->rowCount() !== 0;
    }

    /**
     * Returns last inserted id in the database
     * @param ?string $name
     * 
     * @return string|false
     */
    public function lastId(?string $name = null): string|false
    {
        return $this->conn->lastInsertId($name);
    }

    public function paramsFromAttrs(array $attributes): string
    {
        return join(" AND ", array_map(fn(string $k): string => "$k=:$k", array_keys($attributes)));
    }

    public function getInstance(): self
    {
        return $this;
    }

    /**
     * Returns new query builder
     * @param string $table
     * 
     * @return QueryBuilder
     */
    public function newQueryBuilder(string $table): QueryBuilder
    {
        return new QueryBuilder($table, $this);
    }
}
