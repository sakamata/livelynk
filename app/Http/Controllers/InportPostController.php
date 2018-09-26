<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
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

        // DBにあるPOST前のMACarressを取得
        $stays_macs = DB::table('mac_addresses')->where('current_stay', 1)->pluck('mac_address');
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
            $check = DB::table('mac_addresses')->where('mac_address', $post_mac)->exists();
            if (!$check) {
                // 未登録なら、最低限のinsert 滞在中に変更
                // user_id = 1 は仕様上[ユーザー未登録]のrecord
                $param = [
                    'mac_address' => $post_mac,
                    'vendor' => $check_array["vendor"][$v],
                    'user_id' => 1,
                    'router_id' => $check_array["router_id"],
                    'arraival_at' => $now,
                    'posted_at' => $now,
                    'current_stay' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                DB::table('mac_addresses')->insert($param);

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
                $mac_record = DB::table('mac_addresses')
                    ->where([
                        ['mac_address', $post_mac],
                    ])->first();
                $users_ids[] = $mac_record->user_id;

                // 前回のPOSTに該当MACaddressがない場合
                if (!in_array($post_mac, $stays_mac_array)) {
                    // 来訪/継続ステータスの変更、通知の判断、通知の値取得を行う

                    // 既にこのuserの他のデバイスの存在があるか?
                    $stay = DB::table('mac_addresses')
                        ->where([
                            ['user_id', $mac_record->user_id],
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
                    if ($now >= $limit) {
                        //  該当レコードの来訪時間 arraival_at 更新
                        DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                            'router_id' => $check_array["router_id"],
                            'arraival_at' => $now,
                        ]);
                    Log::debug(print_r('mac arraival_at update now!!!', 1));
                    }

                    // 他のデバイスが無く、かつ不在から一定時間以上だった場合のみ
                    //  push_usersに追加
                    if (!$stay && $now >= $limit) {
                        //  通知の為のuser nameを取得
                        $user = DB::table('users')->where('id', $mac_record->user_id)->first();

                        Log::debug(print_r("来訪者の通知開始 該当user>>>", 1));
                        Log::debug(print_r($user, 1));

                        $person = array(
                            "id" => $user->id,
                            "name" => $user->name,
                        );
                        $push_users[$i] =  $person;
                        $i++;
                    }
                }
            }
            // 登録済みの場合、通知判定を終えた後、テータスを更新する
            // *****ToDo*****【検討】非表示デバイスを撥ねる条件追加しても良いか？
            // 条件追加で非表示デバイスの情報はMACが飛んでいても更新されなくなる
            DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
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

            (new ExportPostController)->push_ifttt($push_users, $category = "arraival");
        }
        $this->DepartureCheck();
    }

    public function HashCheck($check_array)
    {
        // hash確認 touter_id が数値以外なら処理停止
        if (!is_numeric($check_array["router_id"])) {
            Log::debug(print_r('Inport json post router_id not is_numeric!! posted router_id ==> ' .$check_array["router_id"], 1));
            exit();
        } else {
            $router_id = $check_array["router_id"];
        }
        $secret = 'App\AdminRouter'::where('id', $router_id)->value('hash_key');

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
            DB::table('users')->where('id', $id)
            ->update(['last_access' => $now,]);
        }
    }

    public function DepartureCheck()
    {
        // 一定時間アクセスの無いmac_address を不在に変更
        // last_access が 一定時間以上になった全ての current_stay true を false にする
        // ***ToDo*** 同時刻に人感センサー有りなら、帰宅確度を上げる処理を追加

        $now = Carbon::now();
        $second = env("JUDGE_DEPARTURE_INTERVAL_SECOND");
        $past_limit = $now->subSecond($second);

        $went_away = DB::table('mac_addresses')->where([
            ['hide', false],
            ['current_stay', true],
            ['posted_at', '<=', $past_limit],
        ])->get();

        $users_id = array();
        $i = 0;
        foreach ($went_away as $went) {
            Log::debug(print_r("帰ったステータス更新 判定デバイス>>>>", 1));
            Log::debug(print_r($went->id .' '. $went->vendor .' '. $went->device_name, 1));

            DB::table('mac_addresses')->where('id', $went->id)
                ->update([
                    'departure_at' => $now,
                    'current_stay' => false,
                    'updated_at' => $now,
                ]);
            $users_id[$i] = $went->user_id;
            $i++;
        }
        $users_id = array_unique($users_id);
        $push_users = array();
        $i = 0;
        foreach ((array)$users_id as $id) {
            // push通知が必要なuserのidと名前を取得して配列 $push_users に加工
            $user = DB::table('users')->where([
                ['id', $id],
            ])->first();
            // 該当userの非表示以外の滞在中mac_addressが無いか確認
            $exist = DB::table('mac_addresses')->where([
                ['user_id', $id],
                ['hide', false],
                ['posted_at', '>', $past_limit],
            ])->exists();
            // 無ければ通知へのデータ加工
            if (!$exist) {
                $person = array(
                    "id" => $user->id,
                    "name" => $user->name,
                );
                $push_users[$i] =  $person;
                $i++;
            }
        }

        // 滞在者数判断処理～外部機能IFTTTに帰宅通知をPOST
        if ($push_users) {
            Log::debug(print_r("!!!push_ifttt departure pushusers >>>>!!!", 1));
            Log::debug(print_r($push_users, 1));

            (new ExportPostController)->push_ifttt($push_users, $category = "departure");
        }
    }   // end function
}
