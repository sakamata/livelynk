<?php

namespace App\Http\Controllers;

use App\MacAddress;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    // 一般ユーザーのメイン画面、滞在者の一覧を表示する
    public function index(Request $request)
    {
        // 非ログイン と ログイン時で対象の community を取得
        // /index?path=hoge
        if (!Auth::check()) {
            if (!$request->path) { return view('welcome'); }
            $community = $this->GetCommunityFromPath($request->path);
            if (!$community) {
                return redirect('/')->with('message', '存在しないページです');
            }
            $community_id = $community->id;
        } else {
            $community_id = session('community_id');
            if (!$community_id) {
                Auth::logout();
                return redirect('/')->with('message', '再度ログインしなおしてください');
            }
            $community = DB::table('communities')
                ->where('id', $community_id)->first();
        }

        // newcomer! 未登録端末 object 取得処理開始--------------

        // community owner の user_id を取得
        $owner_id = DB::table('communities')
            ->where('id', $community->id)
        ->pluck('user_id');

        // 未登録ユーザーで来訪中のmac_address一覧を取得
        $unregistered = 'App\MacAddress'::where([
                ['community_user_id', $owner_id],
                ['hide', false],
                ['current_stay', true],
        ])->orderBy('arraival_at', 'desc')->get();
        // 未登録端末、滞在率の取得
        $unregistered_rate_array = $this->DepartureRateMake($unregistered, $column='posted_at');

        // I'm here! 滞在者object取得処理開始------------------

        // I'm here 取得の為サブクエリ用の滞在中端末を取得
        $mac_addresses = DB::table('mac_addresses')
            ->select(
                DB::raw("community_user_id, min(arraival_at) as min_arraival_at"))
            ->where([
                ['hide', false],
                ['current_stay', true],
            ])
            ->orderBy('min_arraival_at', 'desc')
            ->groupBy('community_user_id');

        // I'm here! 滞在者object取得
        // user_id, name, min_arraival_at, last_access
        $stays = DB::table('community_user')
            ->select('user_id', 'name', 'min_arraival_at', 'last_access')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
            ->JoinSub($mac_addresses, 'mac_addresses', function($join) {
                $join->on('community_user.id', '=', 'mac_addresses.community_user_id');
            })
            ->where([
                ['user_id', '<>', $owner_id],
                ['community_id', $community_id],
            ])->get();
        // 既存滞在者、滞在率の取得
        $stays_rate_array = $this->DepartureRateMake($stays, $column='last_access');

        // 非滞在者の取得処理開始----------------------

        // 該当コミュニティのuser id を配列で取得 （readerAdmin除外）
        // ****** ここに非表示userを除外する処理を追加
        $users_id_obj = DB::table('community_user')
            ->join('communities_users_statuses' , 'community_user.id', '=', 'communities_users_statuses.id')
            ->where([
                ['user_id', '<>', $owner_id],
                ['community_id', $community_id],
                ['hide', false],
        ])->pluck('user_id');

        // コミュ内ユーザーと滞在中ユーザーから
        // 不在ユーザーの user_id をarrayで取得
        $users_id = $this->ChangeObjectToArray($users_id_obj, $column = null);
        $stays_users_id = $this->ChangeObjectToArray($stays, $column = 'user_id');

        // 不在中のuser_id array
        $not_stay_users_id = array_diff($users_id, $stays_users_id);
        // 非滞在者objctの取得 last_access name
        $not_stays = DB::table('community_user')
            ->select('user_id', 'name', 'last_access')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
            ->where([
                ['community_id', $community->id],
            ])
            ->whereIn('community_user.user_id', $not_stay_users_id)
            ->orderBy('last_access', 'desc')
        ->get();

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

    public function ChangeObjectToArray($object, $column = null)
    {
        $i = 0;
        $array = array();
        foreach ($object as $value) {
            if ($column) {
                $value = $value->$column;
            }
            // クエリビルダで取得したオブジェクトを配列に変換
            $array[$i] = json_decode(json_encode($value), true);
            $i++;
        }
        return $array;
    }
}
