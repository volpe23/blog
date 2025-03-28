<?php

include("./Core/Database.php");
include("./Migrations/Blueprint.php");
include("./Migrations/Schema.php");

use Core\Database;
use Migrations\Blueprint;
use Migrations\Schema;

Schema::$db = new Database("db.db");

Schema::create("users", function (Blueprint $table) {
    $table->id();
    $table->string("username", length: 20)->unique()->nullable(false);
    $table->string("password")->nullable(false);
});
