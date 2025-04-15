<?php

namespace Core\Models;

use Core\Model;

class Users extends Model
{
    public static $identifier = "username";

    protected array $fields = ["username", "password"];

    protected array $hidden = ["password"];
}
