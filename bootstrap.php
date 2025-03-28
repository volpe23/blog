<?php

require BASE_PATH . "config.php";

use Core\App;
use Core\Database;

App::bind(Database::class, fn() => new Database($config["database"]));
