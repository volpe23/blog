<?php

namespace Core\Models;

use Core\Model;

class Post extends Model {

    public function user() {
        $this->belongsTo(Users::class);
    }
}