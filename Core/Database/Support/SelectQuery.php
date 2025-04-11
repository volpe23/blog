<?php

namespace Core\Database\Support;

use Core\Database\Traits\Conditional;
use Core\Database\Traits\Orderable;

class SelectQuery extends QueryBuilder
{
    use Conditional, Orderable;

    protected $query = "SELECT";

    /**
     * @param string $table
     * @param array<int, string> $fields
     * @param bool $disctint
     */
    public function __construct($table, protected $fields = ["*"], $distinct = false)
    {
        parent::__construct($table);
        $this->query = "SELECT " . $distinct ? "DISTINCT " : "" . implode(", ", $fields) . "FROM {$table} ";
    }

    public function __toString()
    {
        return $this->query . $this->buildWheres() . $this->buildOrders();
    }
}
