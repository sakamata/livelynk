<?php

namespace App\Service;

use App\UserStayLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Inportされた滞在者情報の機能処理を行うサービス層
 */
class InportService
{

    /**
    * 本日来訪済みのユーザーを配列から削除する
    * @params array $push_users
    * @return bool|array
     */
    public function todayArraivedUserFilter(array $push_users)
    {
        $i = 0;
        $result = [];
        foreach ($push_users as $user) {
            $arraived = UserStayLog::where('community_user_id', $user['community_user_id'])
                ->whereDate('arraival_at', Carbon::toDay())->exists();
            if (!$arraived) {
                // 今日はじめての場合は配列に入れて返却
                Log::debug(print_r('本日初来訪判定'));
                $result[$i] = $user;
            } else {
                Log::debug(print_r('本日来訪済み、挨拶キャンセル>>>community_user_id:' . $user['community_user_id'] . $user['name'] ,1));
            }
            $i++;
        }
        return $result;
    }
}
