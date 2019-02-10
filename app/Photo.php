<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    protected $keyType = 'string';
    const ID_LENGTH = 12;
    protected $perPage = 3;

    // 自作フィールドはここに入れないとJSONに含まれない
    protected $appends = [
        'url', 'likes_count', 'liked_by_user'
    ];

    // ここに入れたフィールドはAPIで返却するJSONに含まれる。
    protected $visible = [
        'id', 'owner', 'url', 'comments', 'likes_count', 'liked_by_user'
    ];

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);

        if(!array_get($this->attributes, 'id')) {
            $this->setId();
        }
    }

    private function setId() {
        $this->attributes['id'] = $this->getRandomId();
    }

    private function getRandomId() {
        $characters = array_merge(
            range(0, 9), range('a', 'z'), range('A', 'Z'), ['-', '_']
        );
        $length = count($characters);

        $id = "";
        for ($i = 0; $i < self::ID_LENGTH; $i++) {
            $id .= $characters[random_int(0, $length - 1)];
        }
        return $id;
    }

    public function owner() {
        return $this->belongsTo('App\User', 'user_id', 'id', 'users');
    }

    public function comments() {
        return $this->hasMany('App\Comment')->orderBy('id', 'desc');
    }

    public function likes()
    {
        // ManyToManyの関係を表す。Photo1つに所属するいいねと関連づけられる。
        return $this->belongsToMany('App\User', 'likes')->withTimestamps();
    }

    /**
     * urlという名前で改変したフィールドを取得したい: computedプロパティのようなもの
     */
    public function getUrlAttribute() {
        return Storage::cloud()->url($this->attributes['filename']);
    }

    public function getLikesCountAttribute() {
        return $this->likes->count();
    }

    public function getLikedByUserAttribute() {
        if (Auth::guest()) {
            return false;
        }

        return $this->likes->contains(function ($user) {
            return $user->id === Auth::user()->id;
        });
    }
}
