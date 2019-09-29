<?php

namespace App\Service;

use DB;
use Auth;
use App\UserStayLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 *
 */
class UserStayLogService
{
    public function communityCanger($request)
    {
        $communityId = Auth::user()->community_id;
        if (Auth::user()->role == 'superAdmin' && $request->has('community_id')) {
            $communityId = $request->community_id;
        }
        return $communityId;
    }

    public function provisionalGet($request)
    {
        $provisionalArr = [
            'regl' => '1',
            'prov' => '1',
        ];
        if ($request->has('provisional')) {
            $provisionalArr = $request->provisional;
        }
        return $provisionalArr;
    }

    public function getStayLog($communityId, $provisionalArr)
    {
        $arr = [];
        if ($provisionalArr['prov'] == 1) {
            array_push($arr, 0);
        }
        if ($provisionalArr['regl'] == 1) {
            array_push($arr, 1);
        }
        return UserStaylog::with('community_user.user')
        ->select(
            'users_stays_logs.*',
            'users_stays_logs.id AS log_id',
            'community_user.*'
        )
        ->Join('community_user', 'community_user.id', '=', 'users_stays_logs.community_user_id')
        ->Join('users', 'community_user.user_id', '=', 'users.id')
        ->where('community_id', $communityId)
        ->whereIn('provisional', $arr)
        ->orderBy('arraival_at','desc')
        ->paginate(30);
    }

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
    public function arraivalInsertNow(int $community_user_id, $now)
    {
        return DB::table('users_stays_logs')
            ->insert([
                'community_user_id' => $community_user_id,
                'arraival_at'       => $now,
                'last_datetime'     => $now
            ]);
    }

    // 来訪中のユーザーの更新 last_datetimeを更新する
    public function lastDatetimeUpdate(int $community_user_id, string $posted_at)
    {
        return DB::table('users_stays_logs')
            ->where([
                ['community_user_id', $community_user_id],
                ['departure_at', null],
            ])
            ->update(['last_datetime' => $posted_at]);
    }

    // 帰宅判断として該当userの departure_at に last_datetime をupdateする
    public function departurePastTimeUpdate($past_limit)
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
    public function longTermStopAfterStayUsersChangeDeparture($departure_at)
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
