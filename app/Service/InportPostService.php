<?php

namespace App\Service;

use App\UserStayLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

/**
 * Inportされた滞在者情報の機能処理を行うサービス層
 */
class InportPostService
{
    public function JsonValueCheck($json)
    {
        if (!$json) {
            exit();
        };
        $check_array = json_decode($json, true);
        Log::debug(print_r($check_array, 1));
        // hash値が異なる場合はexit() で処理停止
        $this->HashCheck($check_array);
        if (!ctype_digit($check_array['time']) &&
            !ctype_digit($check_array['router_id']) &&
            !ctype_digit($check_array['community_id'])
        ) {
            Log::warning(print_r('json int value not integer!! check json ==> ', 1));
            Log::warning(print_r($check_array, 1));
            exit();
        }
        return $check_array;
    }

    public function HashCheck($check_array)
    {
        // hash確認 router_id が数値以外なら処理停止
        if (!is_numeric($check_array["router_id"])) {
            Log::warning(print_r('Inport json post router_id not integer!! posted router_id ==> ' .$check_array["router_id"], 1));
            exit();
        } else {
            $router_id = $check_array["router_id"];
        }
        $secret = 'App\Router'::Join('communities', 'routers.community_id', '=', 'communities.id')
            ->where('routers.id', $router_id)->pluck('hash_key')->first();
        $time = $check_array["time"];
        $this_side_hash = hash('sha256', $time.$secret);
        $post_hash = $check_array["hash"];
        if ($this_side_hash != $post_hash) {
            Log::warning(print_r('Inport json post hash unmatch !! posted hash ==> ' .$post_hash, 1));
            Log::warning(print_r('Inport json post hash unmatch !! This side hash ==> ' .$this_side_hash, 1));
            exit();
        }
    }


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
                Log::debug(print_r('本日来訪済み、挨拶キャンセル>>>community_user_id:' . $user['community_user_id'] . $user['name'], 1));
            }
            $i++;
        }
        return $result;
    }
}
