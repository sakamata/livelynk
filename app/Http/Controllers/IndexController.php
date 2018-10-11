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
    // 一般ユーザーのメイン画面、滞在者の一覧を表示する
    public function index(Request $request)
    {
        // 非ログイン と ログイン時で対象の community を取得
        // /index?path=hoge
        if (!Auth::check()) {
            if (!$request->path) {
                return view('welcome');
            }

            $community = $this->GetCommunityFromPath($request->path);
            if (!$community) {
                return redirect('/')->with('message', '存在しないページです');
            }
        } else {
            $user = Auth::user();
            $community = DB::table('communities')->where('id', $user->community_id)->first();
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

        $unregistered_rate_array = $this->DepartureRateMake($unregistered, $column='posted_at');

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
        $stays_rate_array = $this->DepartureRateMake($stays, $column='last_access');

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
            'rate' => $unregistered_rate_array,
            'rate1' => $stays_rate_array,
        ]);
    }

    public function DepartureRateMake($items, $column)
    {
        $judge = env('JUDGE_DEPARTURE_INTERVAL_SECOND');
        $now = Carbon::now()->timestamp;
        $limit = $now - Carbon::now()->subSecond($judge)->timestamp;
        $i = 0;
        $res = array();
        foreach ($items as $item) {
            $n[$i] = $now - Carbon::parse($item->$column)->timestamp;
            if($n[$i] >= $limit) {
                $res[$i] = 0;
            } else {
                $res[$i] = 100 - ($n[$i] / $limit) * 100;
                $res[$i] = round($res[$i], 0);
            }
        $i++;
        }
        return $res;
    }

    public function Test()
    {
        return view('index.index0');
    }
}
