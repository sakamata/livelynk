<?php

namespace App\Service;

use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class UserStayLogService
{
    // 来訪した community_user_id で帰宅カラムの入力がない状態が重複していないか確認する
    public function ArraivalUserDuplicationCheck(int $community_user_id)
    {
        return DB::table('users_stays_logs')
            ->where([
                ['community_user_id', $community_user_id],
                ['departure_at', null]
            ])
            ->exists();
    }

    // 来訪中としてrecordを登録する
    public function arraivalInsertNow(int $community_user_id, string $now)
    {
        return DB::table('users_stays_logs')
            ->insert([
                'community_user_id' => $community_user_id,
                'arraival_at' => $now,
                'last_datetime' => $now
            ]);
    }

    // 来訪中のユーザーの更新 last_datetimeを更新する
    public function last_datetimeUpdate(int $community_user_id, string $posted_at)
    {
        return DB::table('users_stays_logs')
            ->where([
                ['community_user_id', $community_user_id],
                ['departure_at', null],
            ])
            ->update(['last_datetime' => $posted_at]);
    }

    // 帰宅判断として該当userの departure_at に last_datetime をupdateする
    public function departurePastTimeUpdate(string $past_limit)
    {
        return DB::table('users_stays_logs')
            ->where([
                ['departure_at', null],
                ['last_datetime', '<', $past_limit],
            ])
        ->update([
            'departure_at' => $past_limit,
        ]);
    }

    // 長期サービス停止後の稼働直後、停止前滞在中だったユーザーを一律で帰宅中に変更する
    public function longTermStopAfterStayUsersChangeDeparture(string $departure_at)
    {
        return DB::table('users_stays_logs')
            ->where([
                ['departure_at', null]
            ])
        ->update([
            'departure_at' => $departure_at
        ]);
    }
}
