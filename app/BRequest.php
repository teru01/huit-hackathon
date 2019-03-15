<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BRequest extends Model
{
    protected $guarded = ['id'];

    protected $visible = ['id', 'user_id', 'book_id', 'accepted'];
}
