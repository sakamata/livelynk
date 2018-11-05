<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\CommunityUser;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InportPostController extends Controller
{
    // MAC アドレス一覧を受け取って、mac_addresses tableへの登録、更新を行う
    public function MacAddress(Request $request)
    {
        $json = $request->json;
        if (!$json) { exit();};
        $check_array = json_decode($json, true);
        Log::debug(print_r($check_array, 1));
        // hash値が異なる場合はexit() で処理停止
        $this->HashCheck($check_array);

        if (
            !ctype_digit($check_array['time']) &&
            !ctype_digit($check_array['router_id']) &&
            !ctype_digit($check_array['community_id'])
        ) {
            Log::debug(print_r('json int value not integer!! check json ==> ', 1));
            Log::debug(print_r($check_array, 1));
            exit();
        }

        // POSTされた community_id の半角英数字から communities table の id を導く
        $community = DB::table('communities')
            ->where('name', $check_array['community_id'])
        ->first();
        $community_id_int = $community->id;

        if (!$community) {
            Log::debug(print_r('community name not found!! check json ==> ', 1));
            Log::debug(print_r($check_array, 1));
            exit();
        }

        // MACアドレス形式のみ大文字にして配列に入れ、それ以外はlog出力
        $post_mac_array = array();
        foreach ((array)$check_array["mac"] as $check) {
            $pattern = preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $check);
            if (!$pattern) {
                Log::debug(print_r('Inport post Not MACaddress!! posted element ==> ' .$check, 1));
            } else {
                $check_MAC = strtoupper($check);
                array_push($post_mac_array, $check_MAC);
            }
        }

        // DBにあるPOST前のMACaddressを取得
        $stays_macs = DB::table('community_user')
            ->leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
        ->where([
            ['community_user.community_id', $community_id_int],
            ['mac_addresses.current_stay', true],
        ])->pluck('mac_address');

        // クエリビルダで取得したオブジェクトを配列に変換
        $stays_mac_array = json_decode(json_encode($stays_macs), true);
        $now = Carbon::now();
        $push_users =array();
        $users_ids = array();
        $i = 0;
        $v = 0;
        // POSTされたMACaddressを個々で精査し来訪判断
        foreach ((array)$post_mac_array as $post_mac) {
            // 登録済みMACアドレスか個別確認
            $check = DB::table('community_user')
                ->leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
            ->where([
                ['community_user.community_id', $community_id_int],
                ['mac_addresses.mac_address', $post_mac],
            ])->exists();

            if (!$check) {
                // 未登録なら、最低限のinsert 滞在中に変更
                // $community->user_id は [未登録] コミュニティ管理者
                $param = [
                    'community_user_id' => $community->user_id,
                    'router_id' => $check_array["router_id"],
                    'mac_address' => $post_mac,
                    'vendor' => $check_array["vendor"][$v],
                    'arraival_at' => $now,
                    'posted_at' => $now,
                    'current_stay' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                DB::table('mac_addresses')->insert($param);
                $community_user_id = $community->user_id;

                // 新規訪問者通知へのpush
                $person = array(
                    "id" => "id未定",
                    "name" => "初来訪者? wi-fi初接続",
                 );
                $push_users[$i] = $person;
                $i++;
            } else {
                // 登録済みMACaddressの場合

                // last_accessの更新をするuserのid一覧を取得
                // *****ToDo***** 非表示端末は配列に入れない事にして、OUT時間が非表示端末の時間を拾うバグに対応する

                $mac_record = DB::table('community_user')
                    ->select('community_user.id', 'departure_at')
                    ->leftJoin('mac_addresses', 'mac_addresses.community_user_id', '=', 'community_user.id')
                ->where([
                    ['community_user.community_id', $community->id],
                    ['mac_addresses.hide', false],
                    ['mac_addresses.mac_address', $post_mac],
                ])->first();
                if (!$mac_record) { continue; }
                $community_user_id = $mac_record->id;
                $users_ids[] = $community_user_id;

                // 前回のPOSTに該当MACaddressがない場合
                if (!in_array($post_mac, $stays_mac_array)) {
                    // 来訪/継続ステータスの変更、通知の判断、通知の値取得を行う

                    // 既にこのuserの他のデバイスの存在があるか?
                    $stay = DB::table('mac_addresses')
                        ->where([
                            ['community_user_id', $community_user_id],
                            ['current_stay', true],
                            ['hide', false],
                    ])->exists();

                    // 前回帰宅時間から、ルーター瞬断や中座に対応した通知処理を行う
                    // 初来訪機器で、帰宅時間がない場合は limit は現在となる
                    $departure_at = new Carbon($mac_record->departure_at);
                    $second = env("JUDGE_ARRAIVAL_INTERVAL_SECOND");
                    $limit = $departure_at->addSecond($second);

                    // 一定時間以上間の空いた場合はDBのステータス滞在中に変更 arraival_at のみ変更
                    // 現在を含むので ">="  が正
                    // ***MEMO*** X hide X, current_stay の条件をDB構造変更(comm_user 追加)対応時に追加
                    // ['hide', false],
                    if ($now >= $limit) {
                        //  該当レコードの来訪時間 arraival_at 更新
                        DB::table('mac_addresses')->where([
                            ['community_user_id', $community_user_id],
                            ['mac_address', $post_mac],
                        ])->update([
                            'router_id' => $check_array["router_id"],
                            'arraival_at' => $now,
                            'current_stay' => true,
                        ]);
                        Log::debug(print_r('mac arraival_at update now!!!', 1));
                    }
                    // 他のデバイスが無く、かつ不在から一定時間以上だった場合のみ
                    //  push_usersに追加
                    if (!$stay && $now >= $limit) {
                        //  通知の為のuser nameを取得
                        // user_id name hide を取得 (非表示ユーザー除外)
                        $user = DB::table('community_user')
                            ->select('user_id', 'name', 'hide')
                            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
                            ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
                            ->where([
                                ['community_user.id', $community_user_id],
                                ['hide', false],
                        ])->first();

                        if ($user) {
                            Log::debug(print_r("来訪者の通知開始 該当user>>>", 1));
                            Log::debug(print_r($user, 1));
                            // ***ToDo*** 重複削除をする処理を追加する 1ユーザーの複数端末が同時到着の際、重複して名前が飛ぶ。
                            $person = array(
                                "id" => $user->user_id,
                                "name" => $user->name,
                            );
                            $push_users[$i] =  $person;
                            $i++;
                        }
                    }
                }
            }
            // 登録済みの場合、通知判定を終えた後、テータスを更新する
            // ***MEMO*** DB変更時に 非表示デバイス hide を撥ねる条件追加
            // 非表示デバイスの情報はMACが飛んでいても更新さない
            DB::table('mac_addresses')->where([
                ['community_user_id', $community_user_id],
                ['mac_address', $post_mac],
                ['hide', false],
            ])->update([
                'router_id' => $check_array["router_id"],
                'current_stay' => true,
                'posted_at' => $now,
                'updated_at' => $now,
            ]);
            $v++;
        } // end foreach

        // 滞在者の id重複削除してからuser table last_accessを更新
        $users_ids = array_unique($users_ids);
        $users_ids = array_values($users_ids);
        $this->user_last_access_update($users_ids, $now);
        // 外部機能IFTTTに来訪通知をPOST
        if ($push_users) {
            Log::debug(print_r("!push_ifttt arraival!>>>>", 1));
            Log::debug(print_r($push_users, 1));

            (new ExportPostController)->push_ifttt($push_users, $category = "arraival", $community->id);
        }
        $this->DepartureCheck($community->id);
    }

    public function HashCheck($check_array)
    {
        // hash確認 router_id が数値以外なら処理停止
        if (!is_numeric($check_array["router_id"])) {
            Log::debug(print_r('Inport json post router_id not integer!! posted router_id ==> ' .$check_array["router_id"], 1));
            exit();
        } else {
            $router_id = $check_array["router_id"];
        }
        $secret = 'App\Router'::where('id', $router_id)->value('hash_key');

        $time = $check_array["time"];
        $this_side_hash = hash('sha256',$time.$secret);
        $post_hash = $check_array["hash"];
        if ($this_side_hash != $post_hash) {
            Log::debug(print_r('Inport json post hash unmatch !! posted hash ==> ' .$post_hash, 1));
            Log::debug(print_r('Inport json post hash unmatch !! This side hash ==> ' .$this_side_hash, 1));
            exit();
        }
    }

    // users table last_accessの一括更新
    public function user_last_access_update($users_ids, $now)
    {
        foreach ($users_ids as $id) {
            DB::table('communities_users_statuses')->where('id', $id)
            ->update(['last_access' => $now,]);
        }
    }

    // ***ToDo*** $community 引数を排除、中で別メソッドから $community を取得させ独立した処理にさせる。さらにタイマー、またはPOSTの止まったコミュニティを探して呼び出し、コミュニティ毎に定期的に確認する処理に変更
    // public function DepartureCheck()
    public function DepartureCheck($community_id)
    {
        // 一定時間アクセスの無いmac_address を不在に変更
        // last_access が 一定時間以上になった全ての current_stay true を false にする
        // ***ToDo*** 同時刻に人感センサー有りなら、帰宅確度を上げる処理を追加
        $now = Carbon::now();
        $second = env("JUDGE_DEPARTURE_INTERVAL_SECOND");
        $past_limit = $now->subSecond($second);

        // 帰宅処理が必要なIDを抽出
            // 端末の current_stay => false
            // user を特定、名前を取得して通知にpush
        // ['community_id', 1],
        $went_away = DB::table('mac_addresses')
        ->select('mac_addresses.id', 'community_user_id', 'user_id')
        ->leftJoin('community_user', 'community_user.id', '=', 'mac_addresses.community_user_id')
        ->where([
            ['community_id', $community_id],
            ['hide', false],
            ['current_stay', true],
            ['posted_at', '<=', $past_limit],
        ])->get();
        if (!$went_away) {
            // log::debug(print_r('$went_away 帰宅判断対象無し、処理停止',1));
            exit();
        }

        // 帰宅判断以上の時間が経過した端末の current_stay やステータスを変更
        $users_id = array();
        $i = 0;
        foreach ($went_away as $went) {
            // $went->id == mac_addresses id
            DB::table('mac_addresses')->where('id', $went->id)
                ->update([
                    'departure_at' => $now,
                    'current_stay' => false,
                    'updated_at' => $now,
            ]);
            $users_id[$i] = $went->user_id;
            $i++;
        }

        // $users_id == 帰宅可能性のある user_id (users の id カラム)
        $users_id = array_unique($users_id);
        $near_push_users_id = array();
        $i = 0;
        foreach ((array)$users_id as $user_id) {
            // push通知が必要な user 選定処理、非表示 user を除外
            // $near_push_users_id に user_id の配列として加工
            $user = DB::table('community_user')
                ->select('community_user.id')
                ->Join('communities_users_statuses', 'communities_users_statuses.id', '=', 'community_user.id')
                ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
                ->where([
                    ['community_id', $community_id],
                    ['user_id', $user_id],
                    ['hide', false],
            ])->first();
            $near_push_users_id[$i] = $user->id;
            $i++;

            // 非表示userは処理せず
            if (!$user) {
                Log::debug(print_r('非表示userの為、帰宅通知をしないuserのid>>> ' . $user_id, 1));
                continue;
            }
        }

        if (!$near_push_users_id) {
            // log::debug(print_r('$near_push_users_id 帰宅判断対象無し、処理停止',1));
            exit();
        }

        // 該当userの非表示以外の滞在中mac_addressが無いか確認
        $no_push_user_id = array();
        $i = 0;
        foreach ($near_push_users_id as $id) {
            $exist = DB::table('mac_addresses')->where([
                ['community_user_id', $id],
                ['hide', false],
                ['posted_at', '>', $past_limit],
            ])->exists();
            if ($exist) {
                $no_push_user_id[$i] = $went->community_user_id;
                $i++;
            }
        }

        // $no_push_user_id を通知から外す
        $push_users_id = array_diff($near_push_users_id, $no_push_user_id);
        if (!$push_users_id) {
            // log::debug(print_r('$push_users_id 帰宅判断対象無し、処理停止',1));
            exit();
        }

        $push_users_obj = DB::table('community_user')
            ->select('user_id', 'name')
            ->leftJoin('users', 'users.id', '=', 'community_user.user_id')
            ->whereIn('community_user.id', $push_users_id)
        ->get();

        // 通知へのデータ加工
        $push_users = array();
        $i = 0;
        foreach ($push_users_obj as $user) {
            $person = array(
                "id" => $user->user_id,
                "name" => $user->name,
            );
            Log::debug(print_r("帰ったステータス更新 判定user>>>>", 1));
            Log::debug(print_r($user->user_id .' '. $user->name, 1));
            $push_users[$i] =  $person;
            $i++;
        }

        // 滞在者数判断処理～外部機能IFTTTに帰宅通知をPOST
        if ($push_users) {
            Log::debug(print_r("!!!push_ifttt departure pushusers >>>>!!!", 1));
            Log::debug(print_r($push_users, 1));

            (new ExportPostController)->push_ifttt($push_users, $category = "departure", $community_id);
        }
    }   // end function
}
