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
        // view table 上側のみ 未登録ユーザーで来訪中のmac_address一覧を取得
        $unregistered = DB::table('mac_addresses')
            ->where([
                ['user_id', 1],
                ['hide', false],
                ['current_stay', true],
            ])
            ->orderBy('arraival_at', 'desc')->get();

        // view tableの中央部分のみ、サブクエリでmacの来訪中mac_addressをuser毎に出す
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

        // view tableの下側のみ、サブクエリでmacの帰宅中mac_addressをuser毎に出す
        $current_not_stays = DB::table('mac_addresses')
            ->select(DB::raw("user_id, max(departure_at) as max_departure_at"))
            ->where([
                ['hide', false],
            ])
            ->groupBy('user_id');

        // 在籍中のuser id を配列で取得 除外対象とする
        $stay_users = DB::table('mac_addresses')->select('user_id')
            ->distinct('user_id')
            ->where('current_stay', true)
            ->orderBy('user_id', 'asc')->get();
        // オブジェクトから単純配列 $not_in への加工
        $stay_users = json_decode(json_encode($stay_users), true);
        $not_in = array();
        foreach ((array)$stay_users as $key => $value) {
            $not_in[] = $value['user_id'];
        }

        // 親クエリでusers table呼び出し
        $not_stays = DB::table('users')
            ->joinSub($current_not_stays, 'current_stays', function($join) {
                $join->on('users.id', '=', 'current_stays.user_id');
            })->where([
                ['id', '<>', 1],
                ['hide', false],
            ])
            ->whereNotIn('id', $not_in)
            ->orderBy('current_stays.max_departure_at', 'desc')->get();

        return view('index.index', [
            'items' => $unregistered,
            'items1' => $stays,
            'items2' => $not_stays,
        ]);
    }
}
