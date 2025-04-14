<?php

namespace Core;

use Core\App;
use Core\Facades\Database as DB;
use Core\Database\Database;
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
        $this->db = DB::getInstance();

        $this->table = $this->table ?? $this->getTableName();
        // $this->qb = new QueryBuilder($this->table, $this->db);

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

    public function createEntry(array $values): void
    {
        // Implement model db creation
        $this->qb->insert($values);
    }

    public static function create(array $attributes): static
    {
        $inst = new static();
        $inst->attributes = $attributes;
        $inst->createEntry($attributes);
        return $inst;
    }

    public static function all(): array
    {
        // return App::resolve(Database::class)->query("SELECT * FROM " . static::getTableName())->fetchAllClass(static::class);
        return [];
    }

    public static function where(string | array $col, mixed $value = NULL, string $comp = "="): QueryBuilder
    {
        $instance = new static();
        return $instance->newQueryBuilder()->select()->where($col, $value, $comp);
    }

    /**
     * Create a new query
     * @return void
     */
    public function newQuery(): void
    {
        $this->qb = $this->newQueryBuilder();
    }

    /**
     * Create a new QueryBuilder for the instance and sets the model to this instance
     * @return Querybuilder
     */
    public function newQueryBuilder(): QueryBuilder
    {
        return (new QueryBuilder($this->table, $this->db))->setModel($this);
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
