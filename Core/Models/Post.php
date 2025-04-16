<?php

namespace Core\Models;

use Core\Model;

class Post extends Model {

    public function user() {
        $this->with("user_id", Users::class);
    }
}