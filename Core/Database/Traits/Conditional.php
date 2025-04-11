<?php

namespace Core\Database\Traits;

use RuntimeException;

trait Conditional
{
    /**
     * All where clause store
     * @var array<array> $wheres
     */
    protected array $wheres = [];
    protected array $bindings = [];

    /**
     * @param string|array $param
     * @param string|null $operator
     * @param string|null $value
     * 
     * @return self
     */
    public function where($param, $operator = "=", $value = null): self
    {
        if (is_array($param)) {
            foreach ($param as $p) {
                if (count($p) !== 3) throw new RuntimeException("wrong where clause");
                $this->setWhere(...$p);
            }
            return $this;
        }

        $this->setWhere($param, $operator, $value);
        return $this;
    }

    /**
     * Sets a single where
     * @param string|array $param
     * @param string|null $operator
     * @param string|null $value
     */
    protected function setWhere($param, $operator = "=", $value = null)
    {
        $this->bindings[$param] = $value;
        $this->wheres = [$param, $operator, $value];
    }

    /**
     * Builds the sql string
     * @return string
     */
    protected function buildWheres(): string
    {
        if (empty($this->wheres)) return "";
        return "WHERE" . implode(" AND ", array_map(fn($where) => $this->buildSingleWhere($where), $this->wheres));
    }

    /**
     * Build a single where clause
     * @param array<int, string> $where
     * 
     * @return string
     */
    protected function buildSingleWhere(array $where): string
    {
        return "{$where[0]} {$where[1]} :{$where[0]}";
    }
}
