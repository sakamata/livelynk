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

        $now = Carbon::now();
        $json = $request->mac;
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

        $push_users =array();
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
                    "id" => array("id未定"),
                    "name" => array("初来訪者? wi-fi初接続"),
                 );
                $push_users[$i] = $person;
                $i++;
            } else {
                // 登録済みの場合
                // users tableの last_access を更新
                $userID = DB::table('mac_addresses')->where('mac_address', $post_mac)->first();

                // ***ToDo*** デバイス重複で同じ id のレコードを何度も更新しちゃうので foreachの外で処理させる
                DB::table('users')->where('id', $userID->user_id)->update([
                    'last_access' => $now,
                ]);

                // 到着直後なら 該当レコードを滞在中に変更 arraival_at 更新
                if (!in_array($post_mac, $stays_mac_array)) {
                    DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                        'arraival_at' => $now,
                        'current_stay' => true,
                        'updated_at' => $now,
                    ]);
                    //  通知の為のuser nameを取得
                    $user = DB::table('users')->where('id', $userID->user_id)->first();
                    $person = array(
                        "id" => $user->id,
                        "name" => $user->name,
                    );
                    $push_users[$i] =  $person;
                    $i++;
                } else {
                    // 登録済で前回POSTも滞在している場合 updated_at のみ更新
                    DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 滞在者数判断処理へ さらに外部機能IFTTTに来訪通知をPOST
        if ($push_users) {
            $this->stay_count_to_ifttt_push($push_users);
        }

        // 帰宅者をPOST値とBD値の比較で判定する
        $departures = array_diff((array)$stays_mac_array, (array)$post_mac_array);
        if ($departures) {
            foreach ((array)$departures as $departure) {
                DB::table('mac_addresses')->where('mac_address', $departure)->update([
                    'departure_at' => $now,
                    'current_stay' => false,
                    'updated_at' => $now,
                ]);
            }
            // ***ToDo*** 帰宅者有りの通知へのpush

        }
    }


    // 滞在中のおおよその人数を抽出
    // 外部機能IFTTTに来訪通知をPOST
    public function stay_count_to_ifttt_push($push_users)
    {
        // 管理user以外の既存user滞在者数
        $existing_count = DB::table('mac_addresses')
            ->distinct()->select('user_id')
            ->where([
                ['user_id','<>', 1],
                ['current_stay', 1],
                ['hide', 0],
            ])->get();
        $existing_count = $existing_count->count();
        Log::debug(print_r($existing_count, 1));

        // 管理user id=1 に紐づいた滞在中のデバイス一覧
        $unknown_count = DB::table('mac_addresses')
            ->where([
                ['current_stay', 1],
                ['hide', 0],
                ['user_id', 1],
            ])
            ->count();
        Log::debug(print_r($unknown_count, 1));

        $about_max = $existing_count + $unknown_count;
        if ($unknown_count > 0) {
            $users_count_str = $existing_count . "～" . $about_max;
        } else {
            $users_count_str = $existing_count;
        }

        (new ExportPostController)->push_ifttt_arraival($push_users, $users_count_str);
    }

}
