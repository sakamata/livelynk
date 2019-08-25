<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserStayLog extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'users_stays_logs';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'arraival_at',
        'departure_at',
        'last_datetime',
        'created_at',
        'updated_at',
    ];

    public function community_user()
    {
        return $this->belongsTo('App\CommunityUser', 'community_user_id');
    }

    public function mac_address()
    {
        return $this->hasMany('App\MacAddress', 'community_user_id', 'community_user_id');
    }
}
