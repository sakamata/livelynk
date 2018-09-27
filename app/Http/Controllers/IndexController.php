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
        // アクセスしてきた際のpathを取得し異常な値は撥ねる
        if (!preg_match("/^[a-zA-Z0-9]+$/", $request->path)) {
            return view('errors.403');
        }
        // 半角英数の path ならDB見に行って match したコミュニティを描画する
        $community = DB::table('communities')->where('url_path', $request->path)->first();
        if (!$community->url_path) {
            return view('errors.403');
        }

        // newcomer 取得 未登録ユーザーで来訪中のmac_address一覧を取得
        $unregistered = DB::table('mac_addresses')
            ->where([
                ['community_id', $community->id],
                ['user_id', $community->user_id],
                ['hide', false],
                ['current_stay', true],
            ])
            ->orderBy('arraival_at', 'desc')->get();

        // I'm here 取得 サブクエリでmacの来訪中mac_addressをuser毎に出す
        $current_stays = DB::table('mac_addresses')
            ->select(DB::raw("user_id, max(arraival_at) as max_arraival_at"))
            ->where([
                ['community_id', $community->id],
                ['hide', false],
                ['current_stay', true],
            ])
            ->orderBy('max_arraival_at', 'desc')
            ->groupBy('user_id');

        // 親クエリでusers table呼び出し
        $stays = 'App\UserTable'::joinSub($current_stays, 'current_stays', function($join) {
                $join->on('users.id', '=', 'current_stays.user_id');
            })->where([
                ['community_id', $community->id],
                ['id', '<>', $community->user_id],
                ['hide', false],
            ])->get();

        // 非滞在者取得の為 除外条件の滞在者のIDを上記と同じ条件で取得
        $stays_ids = 'App\UserTable'::joinSub($current_stays, 'current_stays', function($join) {
                $join->on('users.id', '=', 'current_stays.user_id');
            })->where([
                ['community_id', $community->id],
                ['id', '<>', $community->user_id],
                ['hide', false],
            ])->pluck('id');
        // 非滞在者の取得
        $not_stays = 'App\UserTable'::whereNotIn('id', $stays_ids)
            ->where([
                ['community_id', $community->id],
                ['id', '<>', $community->user_id],
                ['hide', false],
            ])->orderBy('last_access', 'desc')->get();

        return view('index.index', [
            'community' => $community,
            'items' => $unregistered,
            'items1' => $stays,
            'items2' => $not_stays,
        ]);
    }
}
