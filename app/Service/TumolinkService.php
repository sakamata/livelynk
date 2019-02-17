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

    // return bool
    // $column 'maybe_arraival|maybe_departure'
    public function existsTodayPost(int $community_user_id)
    {
        // あるユーザーの2つの日時宣言カラムに本日以降のrecordがあるかを検証
        return DB::table('tumolink')
            ->where('community_user_id', $community_user_id)
            ->where(function($query){
                $query->where('maybe_arraival', '>', Carbon::today())
                      ->orWhere('maybe_departure', '>', Carbon::today());
            })->exists();
    }

    public function getTodayRecord(int $community_user_id)
    {
        // あるユーザーの2つの日時宣言カラムに本日以降のrecordを取得
        return DB::table('tumolink')
            ->where('community_user_id', $community_user_id)
            ->where(function ($query) {
                $query->where('maybe_arraival', '>', Carbon::today())
                    ->orWhere('maybe_departure', '>', Carbon::today());
            })->first();
    }

    // $column 'maybe_arraival|maybe_departure'
    public function updateTime(int $community_user_id, string $column, $time, bool $google_home_push)
    {
        return DB::table('tumolink')
            ->where([
                ['community_user_id', $community_user_id],
            ])->update([
                $column            => $time,
                'google_home_push' => $google_home_push,
            ]);
    }

    // $column 'maybe_arraival|maybe_departure'
    public function updateTimeNull(int $id, string $column)
    {
        return DB::table('tumolink')->where('id', $id)
            ->update([$column => null]);
    }

    public function remove(int $id)
    {
        return DB::table('tumolink')->where('id', $id)->delete();
    }
}
