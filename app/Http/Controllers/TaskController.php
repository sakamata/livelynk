<?php

namespace App\Http\Controllers;

use DB;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaskController extends Controller
{
    // タスクスケジュールで実行　\app\Console\Kernel.php
    public function auto_provisional_user_remove()
    {
        // 1月以上来訪が無い 仮userを削除 端末, community_user id も削除する
        $date = Carbon::now();
        $pastMonth = $date->subMonth(1);
        $res = DB::table('users')
            ->select('users.id as users_id', 'community_user.id as community_user_id', 'mac_addresses.id as mac_id')
            ->join('community_user', 'community_user.user_id', '=', 'users.id')
            ->join('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['mac_addresses.posted_at', '<=', $pastMonth],
                ['users.provisional',  true],
        ])->get();
        log::debug(print_r('schedule TaskController auto_provisional_user_remove run! delete records >>>',1));
        log::debug(print_r($res,1));
        foreach ($res as $key) {
            DB::table('users')->where('id', $key->users_id)->delete();
            DB::table('community_user')->where('id', $key->community_user_id)->delete();
            DB::table('mac_addresses')->where('id', $key->mac_id)->delete();
        }
    }
}
