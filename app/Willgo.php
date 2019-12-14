<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Willgo extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'willgo';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'from_datetime',
        'to_datetime',
        'maybe_departure_datetime',
        'created_at',
        'updated_at',
    ];

    public function community_user()
    {
        return $this->belongsTo('App\CommunityUser', 'community_user_id');
    }
}
