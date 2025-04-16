<?php

namespace Core\Database\Support;

class SqlBuilder
{
    protected array $selectComponents = [
        "columns",
        "table",
        "wheres",
        "orders",
    ];

    /**
     * Created a SELECT db query
     * @param QueryBuilder $builder
     * @param string[]
     * 
     * @return string
     */
    public function createSelectQuery(QueryBuilder $builder, array $columns): string
    {
        $builder->columns = $columns;
        return "SELECT " . implode(" ", $this->compileComponents($builder));
    }

    /**
     * Compiles all the query components
     * @param QueryBuilder $builder
     * 
     * @return array
     */
    public function compileComponents(QueryBuilder $builder): array
    {
        $query = [];
        foreach ($this->selectComponents as $component) {
            if (isset($builder->$component)) {
                $method = "compile" . ucfirst($component);
                $query[] = $this->$method($builder, $builder->$component);
            }
        }

        return $query;
    }

    /**
     * Compiles query columns
     * @param QueryBuilder $builder
     * @param string[] $columns
     * 
     * @return string
     */
    protected function compileColumns(QueryBuilder $builder, ?array $columns = ["*"]): string
    {
        $select = "SELECT " . $builder->distinct ? 'DISTINCT ' : '';

        return $select . join(", ", $columns);
    }

    /**
     * @param QueryBuilder $builder
     * @param string $table
     * 
     * @return string
     */
    protected function compileTable(QueryBuilder $builder, $table): string
    {
        return "FROM $table";
    }

    /**
     * Compilese all where clauses
     * @param QueryBuilder $builder
     * @param array $wheres
     * 
     * @return string
     */
    protected function compileWheres(QueryBuilder $builder, array $wheres): string
    {
        if (count($wheres) > 0) {
            $str = implode(" ", array_map(fn($where) => $this->compileWhere($where), $wheres));
            return "WHERE " . $this->removeLeadingBoolean($str);
        }

        return "";
    }

    /**
     * Compiles a single where
     * @param array $where
     *
     * @return string
     */
    protected function compileWhere(array $where): string
    {
        return $where["boolean"] . " " . $where["column"] . " " . $where["operator"] . " ?";
    }

    public function compileOrders(QueryBuilder $builder, array $orders): string
    {
        if (count($orders) > 0) {
            $str = implode(", ", array_map(fn($order) => implode(" ", $order), $orders));
            return "ORDER BY " . $str;
        }

        return "";
    }

    /**
     * Creates and INSERT query
     * @param QueryBuilder $builder
     * @param array $values
     * 
     * @return string
     */
    public function createInsertQuery(QueryBuilder $builder, array $values): string
    {
        $keyArr = array_keys($values);

        $cols = $this->wrap(implode(", ", $keyArr), "(", ")");
        $vals = $this->wrap(implode(", ", array_map(fn($key) => ":{$key}", $keyArr)), "(", ")");

        return "INSERT INTO {$builder->table} {$cols} VALUES {$vals}";
    }

    /**
     * Wraps the string with provided elems
     * @param string $str
     * @param string $el
     * @param string|null $el2
     * 
     * @return string
     */
    protected function wrap(string $str, string $el, ?string $el2 = null): string
    {
        return $el . $str . $el2 ?? $el;
    }

    /**
     * Removes the leading boolean when all wheres are compiled
     * @param string $where
     * 
     * @return string
     */
    protected function removeLeadingBoolean(string $whereString): string
    {
        return preg_replace("/and |or /i", "", $whereString, 1);
    }
}
