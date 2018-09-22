<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\UserTable;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller
{
    public function welcome()
    {
        return view('welcome');
    }

    // 一般ユーザーのメイン画面、滞在者の一覧を表示する
    public function index(Request $request)
    {
        // newcomer 取得 未登録ユーザーで来訪中のmac_address一覧を取得
        $unregistered = DB::table('mac_addresses')
            ->where([
                ['user_id', 1],
                ['hide', false],
                ['current_stay', true],
            ])
            ->orderBy('arraival_at', 'desc')->get();

        // I'm here 取得 サブクエリでmacの来訪中mac_addressをuser毎に出す
        $current_stays = DB::table('mac_addresses')
            ->select(DB::raw("user_id, max(arraival_at) as max_arraival_at"))
            ->where([
                ['hide', false],
                ['current_stay', true],
            ])
            ->orderBy('max_arraival_at', 'desc')
            ->groupBy('user_id');

        // 親クエリでusers table呼び出し
        $stays = DB::table('users')
            ->joinSub($current_stays, 'current_stays', function($join) {
                $join->on('users.id', '=', 'current_stays.user_id');
            })->where([
                ['id', '<>', 1],
                ['hide', false],
            ])->get();

        // 非滞在者取得の為 除外条件の滞在者のIDを上記と同じ条件で取得
        $stays_ids = DB::table('users')
            ->joinSub($current_stays, 'current_stays', function($join) {
                $join->on('users.id', '=', 'current_stays.user_id');
            })->where([
                ['id', '<>', 1],
                ['hide', false],
            ])->pluck('id');
        // 非滞在者の取得
        $not_stays = DB::table('users')
            ->whereNotIn('id', $stays_ids)
            ->where([
                ['id', '<>', 1],
                ['hide', false],
            ])->get();

        return view('index.index', [
            'items' => $unregistered,
            'items1' => $stays,
            'items2' => $not_stays,
        ]);
    }
}
