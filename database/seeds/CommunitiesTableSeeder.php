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
            'service_name_reading' => 'ギークオフィスえびす',
            'url_path' => 'hoge',
            'hash_key' => 'hoge',
            'ifttt_event_name' => 'livelynk_local_dev',
            'ifttt_webhooks_key' => config("env.ifttt_webhooks_key_seed"),
            'mail_box_domain' => 'linkdesign.jp',
            'tumolink_enable' => 1,
            'google_home_enable' => 1,
            'google_home_weather_enable' => 1,
            'latitude' => '35.645414', //ギークオフィス恵比寿
            'longitude' => '139.713022',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('communities')->insert($param);

        $param = [
            'enable' => 1,
            'user_id' => 2,
            'name' => 'fujimoto_bbb_commu',
            'service_name' => '長い名前の人コミュニティ',
            'service_name_reading' => '',
            'url_path' => 'hoge2',
            'hash_key' => 'hoge2',
            'ifttt_event_name' => 'livelynk_local_dev',
            'ifttt_webhooks_key' => config("env.ifttt_webhooks_key_seed"),
            'mail_box_domain' => 'longname.com',
            'tumolink_enable' => 1,
            'google_home_enable' => 0,
            'google_home_weather_enable' => 1,
            'latitude' => '42.645411', // 屋久島ヤクスギランド
            'longitude' => '139.713012',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('communities')->insert($param);

        $param = [
            'enable' => 1,
            'user_id' => 3,
            'name' => 'random_name',
            'service_name' => 'ランダム名のコミュニティ',
            'service_name_reading' => 'ランダム名のコミュニティ',
            'url_path' => 'hoge3',
            'hash_key' => 'bZsdNLG9RLJ7H0l5jjimnORuDq5nLki3',
            'ifttt_event_name' => 'livelynk_local_dev',
            'ifttt_webhooks_key' => config("env.ifttt_webhooks_key_seed"),
            'mail_box_domain' => 'randonname.com',
            'tumolink_enable' => 1,
            'google_home_enable' => 1,
            'google_home_weather_enable' => 1,
            'latitude' => '32.0339336', // 宮崎県えびの
            'longitude' => '130.6989145',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('communities')->insert($param);

        $param = [
            'enable' => 0,
            'user_id' => 4,
            'name' => 'faker',
            'service_name' => 'サンプルコミュニティ(無効化中)',
            'service_name_reading' => 'サンプルコミュニティ',
            'url_path' => 'hoge4',
            'hash_key' => 'hoge4',
            'ifttt_event_name' => 'livelynk_local_dev',
            'ifttt_webhooks_key' => config("env.ifttt_webhooks_key_seed"),
            'mail_box_domain' => 'sample.com',
            'tumolink_enable' => 1,
            'google_home_enable' => 1,
            'google_home_weather_enable' => 1,
            'latitude' => '35.134943', // 城ヶ島
            'longitude' => '139.6143781',
            'created_at' => $dateTime,
            'updated_at' => $dateTime,
        ];
        DB::table('communities')->insert($param);
    }
}
