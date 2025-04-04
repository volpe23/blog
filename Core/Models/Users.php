<?php

namespace Core\Models;

use Core\Model;

class Users extends Model
{
    public string $identifier = "username";
    protected array $fields = ["username", "password"];
}
