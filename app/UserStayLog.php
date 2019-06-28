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
    // 外部キー制約の為連番定義を外す
    public $incrementing = false;
    // create時に挿入許可しないカラムを設定
    // protected $guarded = array('id', 'departure_at');

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
        return $this->belongsTo('App\CommunityUser');
    }
}
