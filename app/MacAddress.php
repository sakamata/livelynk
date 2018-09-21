<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MacAddress extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'mac_addresses';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'arraival_at',
        'departure_at',
        'posted_at',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function community()
    {
        return $this->belongsTo('App\AdminCommunity', 'community_id');
    }

    public function router()
    {
        return $this->belongsTo('App\AdminRouter', 'router_id');
    }
}
