<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Photo extends Model
{
    protected $keyType = 'string';
    const ID_LENGTH = 12;
    protected $perPage = 3;

    // 自作フィールドはここに入れないとJSONに含まれない
    protected $appends = [
        'url',
    ];

    // ここに入れたフィールドはAPIで返却するJSONに含まれる。
    protected $visible = [
        'id', 'owner', 'url', 'comments'
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

    /**
     * urlという名前で改変したフィールドを取得したい: computedプロパティのようなもの
     */
    public function getUrlAttribute() {
        return Storage::cloud()->url($this->attributes['filename']);
    }
}
