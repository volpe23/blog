<?php

namespace Core;

use Core\App;
use Core\Facades\Database as DB;
use Core\Database\Database;
use Core\Database\Support\QueryBuilder;
use Core\Facades\Database as FacadesDatabase;
use Exception;

abstract class Model
{
    /**
     * Applications database instance
     * @var Database
     */
    protected Database $db;

    /**
     * Db table name that models is connected to
     * @var string
     */
    public readonly string $table;

    /**
     * Tables id identifier
     * @var string
     */
    protected string $primaryKey = "id";

    /**
     * Model attributes that are fetched from db
     * @var array $attributes
     */
    protected array $attributes;

    /**
     * Timestamp fields
     * @var array|bool $timestamps
     */
    protected array | bool $timestamps = ["created_at", "updated_at"];

    protected array $fields;
    // TODO: implement hidden fields
    /**
     * Fields that should not bet attached to model instance
     * @var array $hidden
     */
    protected array $hidden;

    /**
     * Querybuilder instance
     * @var QueryBuilder $qb
     */
    protected QueryBuilder $qb;

    public function __construct(array $attributes = [])
    {
        $this->db = DB::getInstance();

        $this->table = $this->table ?? $this->getTableName();

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
        if (isset($this->hidden) && array_search($prop, $this->hidden)) return null;
        return $this->attributes[$prop] ?? null;
    }

    public function __call($name, $arguments)
    {
        if (in_array($name, $this->qb->methods)) throw new Exception("$name query method does not exist");
        $this->qb->$name(...$arguments);
        return $this;
    }

    public function fill(array $attributes): static
    {
        return $this;
    }

    /**
     * Returns id of the model
     * @return string
     */
    public function id(): string
    {
        return $this->attributes[$this->primaryKey];
    }

    /**
     * Gets the table name based on model class name
     * @return string
     */
    private static function getTableName(): string
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

    /**
     * Sets the id to the model
     * @return void
     */
    private function setId($id): void
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

    /**
     * Creates the database entry
     * @return bool
     */
    public function createEntry(array $values): bool
    {
        return $this->qb->insert($values);
    }

    /**
     * Created database entry and returns model instance
     * @param array $attributes
     * 
     * @return static|bool
     */
    public static function create(array $attributes): static|bool
    {
        $inst = new static();
        $inst->newQuery();
        $inst->attributes = $attributes;
        if ($inst->createEntry($attributes)) return $inst;
        return false;
    }

    /**
     * Get all the records of table from the database
     * @param string[] $columns
     * 
     * @return Model[]
     */
    public static function all(array $columns = ["*"]): array
    {
        $instance = new static();
        return $instance->newQueryBuilder()->get($columns);
    }

    /**
     * Create a new query
     * @return QueryBuilder
     */
    public function newQuery(): QueryBuilder
    {
        return $this->newQueryBuilder();
    }

    /**
     * Create a new QueryBuilder for the instance and sets the model to this instance
     * @return Querybuilder
     */
    public function newQueryBuilder(): QueryBuilder
    {
        return (new QueryBuilder($this->table, $this->db))->setModel($this);
    }

    /**
     * Removes hidden fields from attributes
     * @return void
     */
    protected function removeHiddenFields(): void
    {
        if (isset($this->hidden)) {
            foreach ($this->hidden as $hiddenKey) {
                unset($this->attributes[$hiddenKey]);
            }
        }
    }

    public function belongsTo(string $class)
    {
        // TODO: establishes relation
    }

    public static function with()
    {
        // TODO: implement function that gets the related model
    }

    public static function query(): QueryBuilder
    {
        return (new static)->newQuery();
    }

    public static function __callStatic($method, $args)
    {
        return (new static)::query()->$method(...$args);
    }
}
