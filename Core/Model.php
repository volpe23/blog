<?php

namespace Core;

use Core\App;
use Core\Database;

abstract class Model
{
    protected Database $db;
    public readonly string $table;

    protected array $attributes;
    protected array $timestamps = ["created_at", "updated_at"];

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
        $this->table = $this->getTableName(static::class);
    }

    private function getTableName(string $class)
    {
        $classExpl = explode("\\", $class);
        $explLen = count($classExpl);
        return strtolower($classExpl[$explLen - 1]);
    }

    public function __set(string $prop, mixed $value)
    {
        $this->attributes[$prop] = $value;
    }
    public function __get(string $prop): mixed
    {
        return $this->attributes[$prop] ?? null;
    }

    private function getAttributesString(): string
    {
        return join(", ", array_keys($this->attributes));
    }

    private function getAttributesPlaceholders(): string
    {
        return join(",", array_map(fn($val) => ":" . $val, array_keys($this->attributes)));
    }

    public function save(): void
    {
        $this->db->query("INSERT INTO {$this->table} ({$this->getAttributesString()}) VALUES ({$this->getAttributesPlaceholders()})", $this->attributes);
    }

    public static function create(array $attributes): static
    {
        $inst = new static();
        $inst->attributes = $attributes;
        foreach ($attributes as $prop => $value) {
            $inst->$prop = $value;
        }
        // $inst->save();
        return $inst;
    }
}
