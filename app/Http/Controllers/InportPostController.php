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
        // ***ToDo*** http://php.net/manual/ja/datetime.createfromformat.php
        // PI側から getTime() で渡された日時をミリ秒削ってdatetimeに変換
        // getTime_to_DATETIME(getTime)

        $json = $request->mac;
        $post_mac_array = json_decode($json);
        $now = Carbon::now();

        // 登録済みMACアドレスか個別確認
        foreach ($post_mac_array as $post_mac) {
            $check = DB::table('mac_addresses')->where('mac_address', $post_mac)->exists();

            if (!$check) {
                // 未登録なら、最低限のinsert 滞在中に変更
                $param = [
                    'mac_address' => $post_mac,
                    'arraival_at' => $now,
                    'current_stay' => true,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                DB::table('mac_addresses')->insert($param);
                // ***ToDo*** 新規訪問者通知へのpush

            } else {
                // 登録済みの場合 該当レコードを滞在中に変更 arraival_at 更新
                DB::table('mac_addresses')->where('mac_address', $post_mac)->update([
                    'arraival_at' => $now,
                    'current_stay' => true,
                    'updated_at' => $now,
                ]);
                // ***ToDo*** 訪問者有りの通知へのpush

            }
        }

        // 帰宅者を判定する
        $stays_macs = DB::table('mac_addresses')->where('current_stay', 1)->pluck('mac_address');
        // クエリビルダで取得したオブジェクトを配列に変換
        $stays_mac_array = json_decode(json_encode($stays_macs), true);
        $departures = array_diff($stays_mac_array, $post_mac_array);
        // Log::debug(print_r($departures, 1));
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

    // ***ToDo*** vendorが未登録なら MACアドレスから スクレイピングでメーカー名を自動登録させる処理のみを書く
    public function FunctionName($value='')
    {
        // code...
    }

}
