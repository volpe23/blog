<?php

namespace Core\Database\Support;

class SelectQuery extends QueryBuilder {
    protected $query = "SELECT";
    
    /**
     * Stores WHERE conditions
     * @var array<string> @conditions
     */
    protected $conditions = [];
}