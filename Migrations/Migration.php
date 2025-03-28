<?php

namespace Migrations;

use Core\Database;
use Core\Model;

class Migrator
{

    public Model $model;
    private Database $db;

    public function __construct(Model $model, Database $db)
    {
        $this->model = $model;
        $this->db = $db;
    }

    public function checkTable()
    {
        $this->db->check("SELECT * FROM {$this->model->table}", []);
    }

    public function createTable(Model $model): bool
    {
        $this->db->query("CREATE TABLE IF NOT EXISTS {$this->model->table}", []);
        return true;
    }
}
