<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $guarded = ['id'];

    public function requests() {
        return $this->hasMany('App\BRequest');
    }

    public function owner() {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }
}
