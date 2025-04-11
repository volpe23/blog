<?php

namespace Core\Database\Support;

class SqlBuilder
{
    public function __construct(protected QueryBuilder $builder) {}

    public function createInsertQuery(array $values): string
    {
        $keyArr = array_keys($values);

        $cols = $this->wrap(implode(", ", $keyArr), "(", ")");
        $vals = $this->wrap(implode(", ", array_map(fn($key) => ":{$key}", $keyArr)), "(", ")");

        return "INSERT INTO {$this->builder->table} {$cols} VALUES {$vals}";
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
}
