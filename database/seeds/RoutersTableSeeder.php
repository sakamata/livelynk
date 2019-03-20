<?php

use Illuminate\Database\Seeder;

class RoutersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dateTime = date("Y-m-d H:i:s");

        $param = [
            'community_id' => 1,
            'name' => 'comu1ルーター1号',
            'google_home_name' => '俺の部屋',
            'google_home_mac_address' => '20:DF:B9:34:CC:B3',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 1,
            'name' => 'comu1ルーター2号',
            'google_home_name' => '俺の部屋',
            'google_home_mac_address' => '20:DF:B9:34:CC:B3',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 2,
            'name' => 'comu2ルーター1号',
            'google_home_name' => '俺の部屋',
            'google_home_mac_address' => '20:DF:B9:34:CC:B3',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 2,
            'name' => 'comu2ルーター2号',
            'google_home_name' => '俺の部屋',
            'google_home_mac_address' => '20:DF:B9:34:CC:B3',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 3,
            'name' => 'comu3単独ルーター',
            'google_home_name' => '俺の部屋',
            'google_home_mac_address' => '20:DF:B9:34:CC:B3',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 4,
            'name' => 'comu4単独ルーター',
            'google_home_name' => '俺の部屋',
            'google_home_mac_address' => '20:DF:B9:34:CC:B3',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);
    }
}
