<?php

namespace Core;

use Core\App;
use Core\Database;
use Exception;

abstract class Model
{
    protected Database $db;
    public readonly string $table;

    protected string $primaryKey = "id";
    protected array $attributes;
    protected array $values;
    protected array | bool $timestamps = ["created_at", "updated_at"];
    protected array $fields;
    protected QueryBuilder $qb;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);

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
        $inst->values = array_values($attributes);
        foreach ($attributes as $k => $v) {
            $inst->$k = $v;
        }
        $inst->createEntry();
        return $inst;
    }

    public static function all(): array
    {
        return App::resolve(Database::class)->query("SELECT * FROM " . static::getTableName())->fetchAllClass();
    }

    public static function where(string | array $col, mixed $value, string $comp = "="): static
    {
        $instance = new static();
        $instance->qb->select()->where($col, $value, $comp);

        return $instance;
    }

    public function get(): static
    {
        return $this->db->query($this->qb->getQuery(), $this->qb->getBinds())->fetchClass(static::class);
    }

    // public static function get(array $attributes): static | null
    // {
    // $inst = new static();

    // $res = $inst->db->query("SELECT * FROM {$inst->table} WHERE {$inst->db->paramsFromAttrs($attributes)}", $attributes)->fetch();
    // if ((bool) $res) {
    // $inst->attributes = $res;
    // }

    // return $inst;
    // }
}
