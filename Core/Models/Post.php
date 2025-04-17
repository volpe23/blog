<?php

namespace Core\Models;

use Core\Model;

class Post extends Model
{

    public function user(): Model
    {
        return $this->with("user_id", Users::class);
    }
}
