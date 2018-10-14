<?php

use Illuminate\Database\Seeder;

class CommunitiesTableSeeder extends Seeder
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
            'enable' => 1,
            'user_id' => 1,
            'name' => 'GeekOfficeEbisu',
            'service_name' => 'ギークオフィス恵比寿',
            'url_path' => 'hoge',
            'ifttt_event_name' => 'livelynk_local_dev',
            'ifttt_webhooks_key' => 'cHg0VvN0DIuKFaorEQegpG',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('communities')->insert($param);

        $param = [
            'enable' => 1,
            'user_id' => 2,
            'name' => 'fujimoto_bbb_commu',
            'service_name' => '長い名前の人コミュニティ',
            'url_path' => 'hoge2',
            'ifttt_event_name' => 'dev_info_comm_2',
            'ifttt_webhooks_key' => 'cHg0VvN0DIuKFaorEQegpG',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('communities')->insert($param);

        $param = [
            'enable' => 0,
            'user_id' => 3,
            'name' => 'random_name',
            'service_name' => 'ランダム名のコミュニティ（無効化中）',
            'url_path' => 'hoge3',
            'ifttt_event_name' => 'dev_info_comm_3',
            'ifttt_webhooks_key' => 'cHg0VvN0DIuKFaorEQegpG',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('communities')->insert($param);
    }
}
