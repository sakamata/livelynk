<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TumolinkTableSeeder extends Seeder
{
    // 作成するダミーツモリスト
    // 全ユーザーのrecord分の必要はない
    // 各コミュニティ毎の ツモリスト、帰る予定 readerAdmin以外

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $list = array(4, 5, 6, 13, 14, 15, 22, 23, 24, 41, 43, 45, 48, 50, 52);
        $three = 3;
        foreach ($list as $key => $community_user_id) {
            // $list の 3倍数の要素はGoogleHomeをOFFにする
            $remainder = $three % 3;
            $remainder == 2 ? $push = false : $push = true;
            // 3倍数でツモリ宣言の抜けを設ける、行くのみ、帰るのみ、両方の宣言
            $remainder == 0 ? $arraival = null : $arraival = Carbon::now()->addHour(1);
            $remainder == 1 ? $departure = null : $departure = Carbon::now()->addHour(1);
            $param = [
                'community_user_id' => $community_user_id,
                'maybe_arraival' => $arraival,
                'maybe_departure' => $departure,
                'google_home_push' => $push,
                'created_at' => Carbon::now()->subDay(5),
                'updated_at' => Carbon::now(),
            ];
            DB::table('tumolink')->insert($param);
            $three++;
        }
    }
}
