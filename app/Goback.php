<?php

namespace App;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Goback extends Model
{
    use SoftDeletes;
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'gobacks';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'maybe_departure',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function community_user()
    {
        return $this->belongsTo('App\CommunityUser', 'community_user_id');
    }
}
