<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tumolink extends Model
{
    use HasFactory;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'tumolink';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'maybe_arraival',
        'maybe_departure',
        'created_at',
        'updated_at',
    ];

    public function community_user()
    {
        return $this->belongsTo('App\CommunityUser', 'community_user_id');
    }
}
