<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'communities';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function community_user()
    {
        return $this->hasMany('App\CommunityUser', 'community_id');
    }

    public function router()
    {
        return $this->hasMany('App\Router', 'community_id');
    }

    public function owner()
    {
        return $this->hasOne('App\UserTable', 'id', 'user_id');
    }
}
