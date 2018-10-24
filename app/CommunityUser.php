<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CommunityUser extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'community_user';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'last_access',
        'created_at',
        'updated_at',
    ];

    public function status()
    {
        return $this->hasOne('App\CommunityUserStatus', 'id');
    }

    public function user()
    {
        return $this->belongsTo('App\UserTable');
    }

    public function community()
    {
        return $this->belongsTo('App\Community');
    }

    public function mac_address()
    {
        return $this->hasMany('App\MacAddress');
    }
}
