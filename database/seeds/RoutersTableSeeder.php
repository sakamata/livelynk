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
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 1,
            'name' => 'comu1ルーター2号',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 2,
            'name' => 'comu2ルーター1号',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 2,
            'name' => 'comu2ルーター2号',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

        $param = [
            'community_id' => 3,
            'name' => 'comu3単独ルーター',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('routers')->insert($param);

    }
}
