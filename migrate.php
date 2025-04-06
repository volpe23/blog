<?php

require "./vendor/autoload.php";

use Core\Database;
use Migrations\Actions;
use Migrations\Blueprint;
use Migrations\Schema;

Schema::$db = new Database("db.db");

Schema::create("users", function (Blueprint $table) {
    $table->id();
    $table->string("username", length: 20)->unique()->nullable(false);
    $table->string("password")->nullable(false);
    $table->datetime("created_at");
    $table->datetime("updated_at");
});

Schema::create("post", function (Blueprint $table) {
    $table->id();
    $table->string("title")->nullable(false);
    $table->string("text")->nullable(false);
    $table->int("user_id")->nullable(false);
    $table->datetime("created_at");
    $table->datetime("updated_at");
    $table->foreign("user_id")->references("users", "id")->onDelete(Actions::Cascade);
});
