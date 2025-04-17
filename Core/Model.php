<?php

namespace Core;

use Core\App;
use Core\Facades\Database as DB;
use Core\Database\Database;
use Core\Database\Support\QueryBuilder;

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
    public static string $primaryKey = "id";

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

    /**
     * Array of columns which have been updated
     * @var array $updatedFields
     */
    protected array $updatedFields = [];

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
        if (isset($this->attributes) && array_key_exists($attr, $this->attributes)) $this->updatedFields[] = $attr;
        $this->attributes[$attr] = $val;
    }

    public function __get(string $prop): mixed
    {
        return $this->attributes[$prop] ?? null;
    }

    /**
     * Fills the attributes on model
     * @param array $attributes
     * 
     * @return $this
     */
    public function fill(array $attributes): static
    {
        $this->attributes = $attributes;
        return $this;
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

    /**
     * Sets the id to the model
     * @return void
     */
    private function setId($id): void
    {
        $this->primaryKey = $id;
        $this->attributes["id"] = $id;
    }

    /**
     * Creates the database entry
     * @return bool
     */
    public function createEntry(array $attributes): bool
    {
        return $this->newQuery()->insert($attributes);
    }

    /**
     * Updated database entry based on model attributes
     * @return bool
     */
    public function update(): bool
    {
        $values = array_intersect_key($this->attributes, array_flip($this->updated));
        return $this->newQuery()->update($this->updatedFields, $values);
    }


    /**
     * Creates database entry and returns model instance
     * @param array $attributes
     * 
     * @return static
     */
    public static function create(array $attributes): static
    {
        $model = new static();

        $model->fill($attributes);
        $model->save();

        return $model;
    }

    /**
     * Saves the entry in database
     * @return $this
     */
    public function save(): static
    {
        $this->createOrUpdate();
        return $this;
    }

    /**
     * Determines wether the record should be inserted or updated
     */
    public function createOrUpdate()
    {
        if (array_key_exists($this->primaryKey, $this->attributes)) {
            // Update
            $this->update();
        } else {
            // Create
            $this->createEntry($this->attributes);
        }
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

    /**
     * Returns the model instance based on foreign key
     * @param class-string<Model> $modelClass
     * @param string $relatedField
     * 
     * @return Model
     */
    public function with($modelClass, string $foreignKey, ?string $relatedModelField = null): Model
    {
        $fk = $this->attributes[$foreignKey];
        return $modelClass::where($relatedModelField ?? $modelClass::$primaryKey, "=", $fk)->first();
    }

    /**
     * Created new model instance and returns querybuilder
     * @return QueryBUilder
     */
    public static function query(): QueryBuilder
    {
        return (new static)->newQuery();
    }

    /**
     * When calling a query method on model return a new instance and querybuilder
     * @param string $method
     * @param array $args
     * 
     * @return QueryBuilder
     */
    public static function __callStatic(string $method, array $args): QueryBuilder
    {
        return (new static)::query()->$method(...$args);
    }
}
