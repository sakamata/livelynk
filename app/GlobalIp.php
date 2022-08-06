<?php

namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GlobalIp extends Model
{
    use HasFactory;

    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'global_ips';

    // 日時表記変更の ->format('Y-m-d') を使いたいカラム名を指定する
    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class);
    }

    public static function exists($globalIp)
    {
        return self::where('global_ip', $globalIp)->exists();
    }

    /**
     *
     * @param string $globalIp
     * @return integer|null
     */
    public static function getId($globalIp)
    {
        $model = self::where('global_ip', $globalIp)
            ->first();
        return $model ? $model->id : null;
    }

    public static function isStay($communityId, $globalIp)
    {
        return self::where('community_id', $communityId)
            ->where('global_ip', $globalIp)
            ->exists();
    }
}
