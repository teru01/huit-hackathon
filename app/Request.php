<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Request extends Model
{
    protected $guarded = ['id'];

    protected $visible = ['id', 'user_id', 'accepted'];
}
