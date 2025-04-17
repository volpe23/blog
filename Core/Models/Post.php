<?php

namespace Core\Models;

use Core\Model;

class Post extends Model
{

    /**
     * Returns the related user model instance
     * @return User
     */
    public function user()
    {
        return $this->with(Users::class, "user_id");
    }
}
