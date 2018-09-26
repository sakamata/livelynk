<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminRouter extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'routers';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function community()
    {
        return $this->belongsTo('App\AdminCommunity', 'community_id');
    }

    public function scopeMyCommunity($query, $self_community)
    {
        return $query->where('community_id', $self_community);
    }
}
