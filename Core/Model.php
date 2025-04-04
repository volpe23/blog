<?php

namespace Core;

use Core\App;
use Core\Database;

abstract class Model
{
    protected Database $db;
    public readonly string $table;

    protected string $id;
    protected array $attributes;
    protected array $values;
    protected array | bool $timestamps = ["created_at", "updated_at"];
    protected array $fields;

    public function __construct()
    {
        $this->db = App::resolve(Database::class);
        $this->table = $this->getTableName(static::class);
        if (is_array($this->timestamps)) {
            $this->fields = [...$this->timestamps];
        }
    }

    private function getTableName(string $class)
    {
        $classExpl = explode("\\", $class);
        $explLen = count($classExpl);
        return strtolower($classExpl[$explLen - 1]);
    }

    public function __set(string $attr, $val)
    {
        $this->attributes[$attr] = $val;
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

    private function searchByAttributes()
    {
        $attributeSearchString = [];
        foreach ($this->attributes as $attr) {
            $attributeSearchString[] = "{$attr}=:{$attr}";
        }
        $str = join(" AND ", $attributeSearchString);
        $this->db->query("SELECT * FROM {$this->table} WHERE {$str}", array_combine($this->attributes, $this->values));
    }

    private function checkIfExists(): bool
    {
        return true;
    }

    private function setId($id)
    {
        $this->id = $id;
        $this->attributes["id"] = $id;
    }

    public function save(): void
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
        return $inst;
    }

    public static function get(array $attributes): static | null
    {
        $inst = new static();

        $res = $inst->db->query("SELECT * FROM {$inst->table} WHERE {$inst->db->paramsFromAttrs($attributes)}", $attributes)->fetch();
        if ((bool) $res) {
            $inst->attributes = $res;
        }

        return $inst;
    }
}
