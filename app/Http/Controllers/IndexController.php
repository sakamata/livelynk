<?php

namespace App\Http\Controllers;

use App\Service\CommunityService;
use App\Service\CommunityUserService;
use App\Service\MacAddressService;
use App\Service\TumolinkService;
use App\Service\RouterService;
use App\Service\WillGoService;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class IndexController extends Controller
{
    private $call_community_user;
    private $call_tumolink;
    private $callRouter;
    private $willGoService;

    public function __construct(
        CommunityService        $call_community,
        CommunityUserService    $call_community_user,
        MacAddressService       $call_mac_address,
        TumolinkService         $call_tumolink,
        RouterService           $callRouter,
        WillGoService           $willGoService
    ) {
        $this->call_community       = $call_community;
        $this->call_community_user  = $call_community_user;
        $this->call_mac_address     = $call_mac_address;
        $this->call_tumolink        = $call_tumolink;
        $this->callRouter           = $callRouter;
        $this->willGoService        = $willGoService;
    }

    // 一般ユーザーのメイン画面、滞在者の一覧を表示する
    public function index(Request $request)
    {
        // 非ログイン と ログイン時で対象の community を取得
        // /index?path=hoge
        if (!Auth::check()) {
            if (!$request->path) {
                return view('site.home');
            }
            $community = $this->GetCommunityFromPath($request->path);
            if (!$community) {
                return view('errors.404');
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

        // I'm here Newcomer 取得------------------------------

        // community owner の user_id を取得
        $owner_id = $this->call_community->GetOwnerUserID((int)$community_id);
        // サブクエリ用の滞在中端末を取得
        $sub_query = $this->call_mac_address->GetStayMacAddressForSubQuery();
        // newcomer! 仮ユーザーフラグのuserを抽出--------------
        $unregistered  = $this->call_community_user->StayUsersGet(
            $sub_query,
            (int)$owner_id,
            (int) $community_id,
            (bool)$provisional = true
        );
        // 未登録端末、滞在率の取得
        $unregistered_rate_array = $this->DepartureRateMake($unregistered, $column = 'last_access');

        // I'm here! 滞在者object取得処理開始------------------
        $stays = $this->call_community_user->StayUsersGet(
            $sub_query,
            (int)$owner_id,
            (int)$community_id,
            (bool)$provisional = false
        );
        // 既存滞在者、滞在率の取得
        $stays_rate_array = $this->DepartureRateMake($stays, $column='last_access');

        // 非滞在者の取得-------------------------------------
        // 該当コミュニティのuser id を配列で取得 （非表示user,readerAdmin除外）
        $users_id_obj = $this->call_community_user->GetNotStayUsersIdObjectArray(
            (int)$owner_id,
            (int)$community_id
        );
        // コミュ内ユーザーと滞在中ユーザーから 不在ユーザーの user_id をarrayで取得
        $users_id = $this->ChangeObjectToArray($users_id_obj, $column = null);
        $unregistered_users_id = $this->ChangeObjectToArray($unregistered, $column = 'user_id');
        $stays_users_id = $this->ChangeObjectToArray($stays, $column = 'user_id');
        $stays_community_user_id = $this->ChangeObjectToArray($stays, $column = 'id');
        $all_stays_users_id = array_merge($unregistered_users_id, $stays_users_id);
        // 不在中のuser_id array
        $not_stay_users_id = array_diff($users_id, $all_stays_users_id);
        // 非滞在者objctの取得 last_access name
        $not_stays = $this->call_community_user->NotStayUsersGet(
            (int)$community->id,
            (array)$not_stay_users_id
        );

        $reader_id = "";
        $isStay = false;
        if (Auth::check()) {
            $reader_id = $this->getReaderID();
            in_array(Auth::user()->id, $stays_community_user_id) ? $isStay = true : $isStay = false;
        }

        $gobackUsers = $this->willGoService->gobackUsers($community_id, $stays_community_user_id);
        $staysUsersId     = $stays->pluck('id')->toArray();
        // 本日ヨテイ宣言をしたユーザーのリストを取得
        // 滞在中のユーザーオブジェクト一覧を取得（滞在中のユーザーを除外）
        $todayWillgoUsers = $this->willGoService->todayWillgoUsers($community_id, $staysUsersId);

        // ヨテイ機能のユーザー一覧取得（滞在中のユーザーを除外）
        $willgoUsers      = $this->willGoService->willGoUsersGet($community_id, $staysUsersId);
        // 本日以降のコミュニティごとの予定宣言の件数を取得
        $willgoCount   = $this->willGoService->willGoCountGet($community_id);
        // 今日来訪を宣言したユーザーのidを配列で取得
        $todayWillgoUsersIds = $this->willGoService->getTodayWillgoUsersIds($community_id);
        // 配列内の重複をcount 本日以降の来訪者宣言件数から countされた値を引いて
        // 本日滞在中のユーザーの来訪宣言を抜いた予定宣言件数を取得
        $subCount = count(array_intersect($stays_community_user_id, $todayWillgoUsersIds));
        $willgoCount = $willgoCount - $subCount;

        $willgoPullDownList = $this->willGoService->willgoPullDownListGet();
        $router = $this->callRouter->CommunityRouterGet($community_id);
        return view('index.index', [
            'isStay'                => $isStay,
            'community'             => $community,
            'todayWillgoUsers'      => $todayWillgoUsers,
            'items'                 => $unregistered,
            'items1'                => $stays,
            'items2'                => $not_stays,
            'gobackUsers'           => $gobackUsers,
            'willgoUsers'           => $willgoUsers,
            'willgoCount'           => $willgoCount,
            'willgoPullDownList'    => $willgoPullDownList,
            'rate'                  => $unregistered_rate_array,
            'rate1'                 => $stays_rate_array,
            'reader_id'             => $reader_id,
            'router'                => $router,
        ]);
    }

    public function DepartureRateMake($items, $column)
    {
        
        $judge = config("env.judge_departure_interval_second");
        $now = Carbon::now()->timestamp;
        $limit = $now - Carbon::now()->subSecond($judge)->timestamp;
        $i = 0;
        $res = array();
        foreach ($items as $item) {
            $n[$i] = $now - Carbon::parse($item->$column)->timestamp;
            if ($n[$i] >= $limit) {
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
