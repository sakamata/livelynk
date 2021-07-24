<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class MailBoxName extends Model
{
    /**
     * モデルと関連しているテーブル
     *
     * @var string
     */
    protected $table = 'mail_box_names';
    protected $fillable = [
        'mail_box_name',
        'community_user_id',
        'global_ip_id',
        'arraival_at',
        'departure_at',
        'current_stay',
        'posted_at',
    ];

    public function communityUser()
    {
        return $this->hasMany(CommunityUser::class);
    }

    /**
     * @param array $communityUserIds
     * @param string $mailBoxName
     * @return integer|null
     */
    public static function getCommunityUserId(array $communityUserIds, string $mailBoxName)
    {
        return self::where('mail_box_name', $mailBoxName)
            ->whereIn('community_user_id', $communityUserIds)
            ->pluck('community_user_id')
            ->first();
    }

    public static function store($request, $communityUserId)
    {
        self::create([
            'mail_box_name' => $request->name,
            'community_user_id' => $communityUserId,
            'global_ip_id' => GlobalIp::getId($request->global_ip),
            'current_stay' => 0,
            'posted_at' => Carbon::now(),
        ]);
    }
}
