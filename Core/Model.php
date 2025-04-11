<?php

namespace Core;

use Core\App;
use Core\Facades\Database;
use Core\Database\Support\QueryBuilder;
use Exception;

abstract class Model
{
    protected Database $db;
    public readonly string $table;

    protected string $primaryKey = "id";
    protected array $attributes;
    protected array | bool $timestamps = ["created_at", "updated_at"];
    protected array $fields;
    // TODO: implement hidden fields
    protected QueryBuilder $qb;

    public function __construct()
    {
        $this->db = Database::getInstance();

        $this->table = $this->table ?? $this->getTableName();
        $this->qb = new QueryBuilder($this->table);

        if (is_array($this->timestamps)) {
            $this->fields = [...$this->timestamps];
        }
    }

    public function __set(string $attr, $val)
    {
        $this->attributes[$attr] = $val;
    }
    public function __get(string $prop): mixed
    {
        return $this->attributes[$prop] ?? null;
    }

    public function __call($name, $arguments)
    {
        if (in_array($name, $this->qb->methods)) throw new Exception("$name query method does not exist");
        $this->qb->$name(...$arguments);
        return $this;
    }

    public function id()
    {
        return $this->attributes[$this->primaryKey];
    }

    private static function getTableName()
    {
        $classExpl = explode("\\", static::class);
        $explLen = count($classExpl);
        return strtolower($classExpl[$explLen - 1]);
    }

    private function getAttributesString(): string
    {
        return join(", ", array_keys($this->attributes));
    }

    private function getAttributesPlaceholders(): string
    {
        return join(",", array_map(fn($val) => ":" . $val, array_keys($this->attributes)));
    }


    private function setId($id)
    {
        $this->primaryKey = $id;
        $this->attributes["id"] = $id;
    }

    public function save(): void
    {
        $this->db->query("INSERT INTO {$this->table} ({$this->getAttributesString()}) VALUES 
        ({$this->getAttributesPlaceholders()})", $this->attributes);

        $this->setId($this->db->lastId($this->table));
    }

    public function createEntry(): void
    {
        $this->db->query("INSERT INTO {$this->table} ({$this->getAttributesString()}) VALUES 
        ({$this->getAttributesPlaceholders()})", $this->attributes);

        $this->setId($this->db->lastId($this->table));
    }

    public static function create(array $attributes): static
    {
        $inst = new static();
        $inst->attributes = $attributes;
        $inst->createEntry();
        return $inst;
    }

    public static function all(): array
    {
        return App::resolve(Database::class)->query("SELECT * FROM " . static::getTableName())->fetchAllClass(static::class);
    }

    public static function where(string | array $col, mixed $value = NULL, string $comp = "="): static
    {
        $instance = new static();
        $instance->qb->select()->where($col, $value, $comp);

        return $instance;
    }

    /**
     * @return static[]
     */
    public function get(): array
    {
        return $this->db->query($this->qb->getQuery(), $this->qb->getBinds())->fetchAllClass(static::class);
    }

    public function first(): static
    {
        return $this->db->query($this->qb->getQuery(), $this->qb->getBinds())->fetchClass(static::class);
    }

    public function belongsTo(string $class)
    {
        // TODO: establishes relation
    }

    public static function with()
    {
        // TODO: implement function that gets the related model
    }
}
