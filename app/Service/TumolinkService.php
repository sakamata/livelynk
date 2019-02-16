<?php

namespace App\Service;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class TumolinkService
{
    public function tumolistGet(int $community_id)
    {
        return DB::table('tumolink')
            ->select(
                'tumolink.*',
                'users.name',
                'users.name_reading',
                'users.provisional',
                'communities_users_statuses.hide'
            )
            ->join('community_user', 'community_user.id', '=', 'tumolink.community_user_id')
            ->join('communities_users_statuses', 'community_user.id', '=', 'communities_users_statuses.id')
            ->join('users', 'users.id', '=', 'community_user.user_id')
            ->where('community_user.community_id', $community_id)
            ->get();
    }

    // $column 'maybe_arraival|maybe_departure'
    public function existsTodayPost(int $community_user_id, string $column)
    {
        return DB::table('tumolink')
            ->where([
                ['community_user_id', $community_user_id],
                [$column, '>', Carbon::today()],
            ])->exists();
    }

    // $column 'maybe_arraival|maybe_departure'
    public function updateTime(int $community_user_id, string $column, $time, bool $google_home_push)
    {
        return DB::table('tumolink')
            ->where([
                ['community_user_id', $community_user_id],
                [$column, '>', Carbon::today()],
            ])->update([
                $column            => $time,
                'google_home_push' => $google_home_push,
            ]);
    }
}
