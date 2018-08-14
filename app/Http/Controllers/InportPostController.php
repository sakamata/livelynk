<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
// test commit comment
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
        foreach ($check_mac_array as $check_mac) {
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
                // ***ToDo*** 新規訪問者通知へのpush

            } else {
                // 登録済みの場合
                // users tableの last_access を更新
                $user = DB::table('mac_addresses')->where('mac_address', $post_mac)->first();

                // ***ToDo*** デバイス重複で同じ id のレコードを何度も更新しちゃうので foreachの外で処理させる
                DB::table('users')->where('id', $user->user_id)->update([
                    'last_access' => $now,
                ]);

                // 到着直後なら 該当レコードを滞在中に変更 arraival_at 更新
                if (!in_array($post_mac, $stays_mac_array)) {
                    DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                        'arraival_at' => $now,
                        'current_stay' => true,
                        'updated_at' => $now,
                    ]);
                    // ***ToDo*** 訪問者有りの通知へのpush

                } else {
                    // 登録済で前回POSTも滞在している場合 updated_at のみ更新
                    DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        // 帰宅者をPOST値とBD値の比較で判定する
        $departures = array_diff((array)$stays_mac_array, (array)$post_mac_array);
        if ($departures) {
            foreach ($departures as $departure) {
                DB::table('mac_addresses')->where('mac_address', $departure)->update([
                    'departure_at' => $now,
                    'current_stay' => false,
                    'updated_at' => $now,
                ]);
            }
            // ***ToDo*** 帰宅者有りの通知へのpush

        }
    }

    // ***ToDo*** vendorが未登録なら MACアドレスから スクレイピングでメーカー名を自動登録させる処理のみをexportControllerに書く
    public function FunctionName($value='')
    {
        // code...
    }

}
