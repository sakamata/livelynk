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
        // ***ToDo*** CSRF対策　独自tokenでバリデート
        // PI側から getTime() で渡された日時をミリ秒削ってdatetimeに変換
        // getTime_to_DATETIME(getTime)

        $json = $request->mac;
        if (!$json) { exit();};
        $check_mac_array = json_decode($json);
        Log::debug(print_r($json, 1));

        // MACアドレス形式のみ大文字にして配列に入れ、それ以外はlog出力
        $post_mac_array = array();
        foreach ((array)$check_mac_array as $check_mac) {
            $pattern = preg_match('/([a-fA-F0-9]{2}[:|\-]?){6}/', $check_mac);
            if (!$pattern) {
                Log::debug(print_r('Inport post Not MACaddress!! posted element ==> ' .$check_mac, 1));
            } else {
                $check_MAC = strtoupper($check_mac);
                array_push($post_mac_array, $check_MAC);
            }
        }

        // DBにあるPOST前の滞在者を取得
        $stays_macs = DB::table('mac_addresses')->where('current_stay', 1)->pluck('mac_address');
        // クエリビルダで取得したオブジェクトを配列に変換
        $stays_mac_array = json_decode(json_encode($stays_macs), true);
        $now = Carbon::now();
        $push_users =array();
        $users_ids = array();
        $i = 0;
        // 登録済みMACアドレスか個別確認
        foreach ((array)$post_mac_array as $post_mac) {
            $check = DB::table('mac_addresses')->where('mac_address', $post_mac)->exists();
            if (!$check) {
                // 未登録なら、最低限のinsert 滞在中に変更
                // user_id = 1 は仕様上[ユーザー未登録]のrecord
                $param = [
                    'mac_address' => $post_mac,
                    'user_id' => 1,
                    'arraival_at' => $now,
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
                // 登録済みの場合
                $mac_record = DB::table('mac_addresses')
                    ->where('mac_address', $post_mac)->first();
                // last_accessの更新をするuserのid一覧を取得
                $users_ids[] = $mac_record->user_id;

                // 到着直後なら
                if (!in_array($post_mac, $stays_mac_array)) {
                    // 既に他のデバイスの存在があるか?
                    $stay = DB::table('mac_addresses')
                        ->where([
                            ['user_id', $mac_record->user_id],
                            ['current_stay', true],
                        ])->exists();

                    // 前回帰宅時間から、ルーター瞬断や中座に対応した通知処理を行う
                    $departure_at = new Carbon($mac_record->departure_at);
                    $limit = $departure_at->addHour(1);
                    // $limit = $departure_at->addSecond(60);
                    // 他のデバイスが無く、かつ不在から一定時間以上だった
                    // 場合のみ push_usersに追加する
                    if (!$stay && $now >= $limit) {
                        //  通知の為のuser nameを取得
                        $user = DB::table('users')->where('id', $mac_record->user_id)->first();
                        $person = array(
                            "id" => $user->id,
                            "name" => $user->name,
                        );
                        $push_users[$i] =  $person;
                        $i++;
                    }
                    //  該当レコードを滞在中に変更 arraival_at 更新
                    DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                        'arraival_at' => $now,
                        'current_stay' => true,
                        'updated_at' => $now,
                    ]);

                } else {
                    // 登録済で前回POSTも滞在している場合 updated_at のみ更新
                    DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                        'updated_at' => $now,
                    ]);
                }
            }
        } // end foreach

        // 滞在者の id重複削除してからuser table last_accessを更新
        $users_ids = array_unique($users_ids);
        $users_ids = array_values($users_ids);
        $this->user_last_access_update($users_ids, $now);

        // 滞在者数判断処理～外部機能IFTTTに来訪通知をPOST
        if ($push_users) {
            (new ExportPostController)->push_ifttt_arraival($push_users);
        }

        // 帰宅者をPOST値とDB値の比較で判定する
        $departures = array_diff((array)$stays_mac_array, (array)$post_mac_array);
        // 前回POSTと比較して存在しないMACアドレスに対しての処理
        if ($departures) {
            // ***ToDo*** 帰宅ステータスのディレイ処理、一定時以上で current_stay falseとする処理を追加
            foreach ((array)$departures as $departure) {
                // 帰宅者デバイスのステータス変更
                DB::table('mac_addresses')->where('mac_address', $departure)->update([
                    'departure_at' => $now,
                    'updated_at' => $now,
                ]);
            }

        }

        // 一定時間アクセスの無いmac_address を不在に変更
        // last_accessが 一定時間以上になった全ての current_stay true を false にする
        // $d_limit = $now->subHour(1);
        // $d_limit = $now->subSecond(120);
        $went_away = DB::table('users')
            ->join('mac_addresses', function($join){
                $now = Carbon::now();
                $d_limit = $now->subHour(1);
                // $d_limit = $now->subSecond(30);
                $join->on('users.id', '=', 'mac_addresses.user_id')
                ->where([
                    ['hide', false],
                    ['current_stay', true],
                    ['last_access', '<=', $d_limit],
                ]);
            })->get();

        foreach ($went_away as $went) {
            Log::debug(print_r("foreach処理 current_stay=>false", 1));
            Log::debug(print_r($went->id, 1));
            Log::debug(print_r($went->device_name, 1));

            DB::table('mac_addresses')->where('id', $went->id)
            ->update(['current_stay' => false,]);
        }

        // ***ToDo*** current_stay  false になったユーザーを 帰宅者有りの通知pushをする

    }

    // users table last_accessの一括更新
    public function user_last_access_update($users_ids, $now)
    {
        foreach ($users_ids as $id) {
            DB::table('users')->where('id', $id)
            ->update(['last_access' => $now,]);
        }
    }

}
