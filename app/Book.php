<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = ['id'];

    public function requests() {
        return $this->hasMany('App\Request');
    }
}
