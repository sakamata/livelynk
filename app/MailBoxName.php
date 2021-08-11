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

    public const STAY = 1;
    public const NOT_STAY = 0;
    public const MAX_MINUTES_JUDGE_STAY = 30;

    public function communityUser()
    {
        return $this->hasOne(CommunityUser::class, 'id', 'community_user_id');
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

    public static function store($request, int $communityUserId)
    {
        self::create([
            'mail_box_name' => $request->name,
            'community_user_id' => $communityUserId,
            'global_ip_id' => GlobalIp::getId($request->global_ip),
            'current_stay' => self::NOT_STAY,
            'posted_at' => Carbon::now(),
        ]);
    }

    public function setArraival(int $communityUserId, $request)
    {
        return $this->where('community_user_id', $communityUserId)
            ->update([
                'global_ip_id' => GlobalIp::getId($request->global_ip),
                'arraival_at' => Carbon::now(),
                'current_stay' => self::STAY,
                'posted_at' => Carbon::now(),
            ]);
    }

    public function setStayUpdate(int $communityUserId, $request)
    {
        return $this->where('community_user_id', $communityUserId)
            ->update([
                'global_ip_id' => GlobalIp::getId($request->global_ip),
                'current_stay' => self::STAY,
                'posted_at' => Carbon::now(),
            ]);
    }

    public function setDeparture(int $communityUserId)
    {
        return $this->where('community_user_id', $communityUserId)
            ->update([
                'departure_at' => Carbon::now(),
                'current_stay' => self::NOT_STAY,
                'posted_at' => Carbon::now(),
            ]);
    }

    public static function isFirstArraival(int $communityUserId, $request)
    {
        $self = self::where('community_user_id', $communityUserId)
            ->where('mail_box_name', $request->name)
            ->get()->first();

        if (is_null($self->departure_at)) {
            if ($self->arraival_at === $self->posted_at) {
                return true;
            }
        }
        return false;
    }

    public static function isArraival(int $communityUserId, $request)
    {
        return self::where('community_user_id', $communityUserId)
            ->where('mail_box_name', $request->name)
            ->where('current_stay', self::NOT_STAY)
            ->exists();
    }

    public static function getNowDepartures(array $communityUserIds, $minutes = null)
    {
        $minutes = $minutes ?? self::MAX_MINUTES_JUDGE_STAY;

        return self::where('current_stay', self::STAY)
            ->whereIn('community_user_id', $communityUserIds)
            ->whereNotNull('arraival_at')
            ->where('posted_at', '<', Carbon::now()->subMinutes($minutes))
            ->get();
    }
}
