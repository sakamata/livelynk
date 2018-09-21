<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminCommunity extends Model
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

    public function owner()
    {
        return $this->hasOne('App\UserTable', 'community_id');
    }

    public function router()
    {
        return $this->hasMany('App\AdminRouter', 'community_id');
    }
}
