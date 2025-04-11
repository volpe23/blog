<?php

namespace Core\Database\Support;

class SqlBuilder
{
    protected array $selectComponents = [
        "columns",
        "table",
        "wheres"
    ];

    public function createSelectQuery(QueryBuilder $builder): string
    {
        return implode(" ", $this->compileComponents($builder));
    }
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

    protected function compileColumns(QueryBuilder $builder, $columns): string
    {
        $select = "SELECT " . $builder->distinct ? 'DISCTINCT ' : '';

        return $select . join(", ", $columns);
    }

    protected function compileTable(QueryBuilder $builder, $table): string
    {
        return "FROM $table";
    }

    protected function compileWheres(QueryBuilder $builder, array $wheres): string
    {
        if (count($wheres) > 0) {
            $str = implode(" ", array_map(fn($where) => $this->compileWhere($where), $wheres));
            return "WHERE " . $this->removeLeadingBoolean($str);
        }

        return "";
    }

    protected function compileWhere(array $where): string
    {

        return $where["boolean"] . " " . $where["column"] . " " . $where["operator"] . " ?";
    }

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

    protected function removeLeadingBoolean(string $whereString): string
    {
        return preg_replace("/and |or /i", "", $whereString, 1);
    }
}
