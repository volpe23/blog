<?php

use Core\Models\Users;
const DATABASE = "db.db";

$config = [
    "database" => BASE_PATH . DATABASE,
    "user_table" => \Core\Models\Users::class
];
