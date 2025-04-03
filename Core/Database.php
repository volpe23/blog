<?php

namespace Core;

use PDO;
use PDOStatement;

class Database
{
    private PDO $conn;
    private PDOStatement $statement;

    public function __construct(private string $file)
    {
        $this->conn = new PDO("sqlite:{$file}", null, null, [
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }

    public function insert(string $query, array $attrs): bool
    {
        $this->statement = $this->conn->prepare($query, $attrs);
        return $this->statement->execute();
    }

    public function query(string $query, array $attrs = []): Database
    {
        var_dump($query, $attrs);
        $this->statement = $this->conn->prepare($query);
        if (!$this->statement->execute($attrs)) echo "Query failed";

        return $this;
    }

    public function fetchAll(): array
    {
        return $this->statement->fetchAll();
    }

    public function fetch(): mixed
    {
        return $this->statement->fetch();
    }

    public function check(string $query, array $attrs = []): bool
    {
        return $this->query($query, $attrs)->statement->rowCount() !== 0;
    }

    public function lastId(?string $name = null): string|false
    {
        return $this->conn->lastInsertId($name);
    }

    public function paramsFromAttrs(array $attributes): string
    {
        return join(" AND ", array_map(fn(string $k): string => "$k=:$k", array_keys($attributes)));
    }
}
