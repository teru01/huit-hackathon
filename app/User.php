<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $visible = [
        'name',
    ];

    /**
     * photosテーブルとのリレーションをモデルで定義する。
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos() {
        return $this->hasMany('App\Photo');
    }
}
