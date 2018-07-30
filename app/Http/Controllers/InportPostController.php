<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class InportPostController extends Controller
{
    public function MacAddress(Request $request)
    {
        // ***ToDo*** CSRF対策　独自tokenでバリデート
        // ***ToDo*** http://php.net/manual/ja/datetime.createfromformat.php
        // PI側から getTime() で渡された日時をミリ秒削ってdatetimeに変換
        // getTime_to_DATETIME(getTime)

        $json = $request->mac;
        $macArray = json_decode($json);
        Log::debug(print_r($macArray,1));

        // 登録済みMACアドレスか確認
        foreach ($macArray as $mac) {

            $check = DB::table('mac_addresses')->where('mac_address', $mac)->exists();
            Log::info(print_r($mac,1));

            $now = Carbon::now();
            $param = [
                'mac_address' => $mac,
                'arraival_at' => $now,
                'current_stay' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];

            if (!$check) {
                // 未登録なら、最低限のinsert
                DB::table('mac_addresses')->insert($param);
            } else {
                // 登録済みの場合 該当レコードのupdate_at更新
                DB::table('mac_addresses')->where('mac_address', $mac)->update([
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

        // 無いものがあれば current drop

    }

}
