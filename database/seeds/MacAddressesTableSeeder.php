<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MacAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();
        $subHour = $now->subHour();
        $subHour = $now->subHour();
        $subSecond = $now->subSecond(30);
        $subDay = $now->subDay(5);

        $param = [
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'vendor' => 'Apple.inc',
            'device_name' => 'i-phoneX',
            'user_id' => 1,
            'hide' => 0,
            'arraival_at' => $subHour,
            'departure_at' => $now->subHour(24),
            'posted_at' => $subSecond,
            'current_stay' => 1,
            'created_at' => $subDay,
            'updated_at' => $subSecond,
        ];
        DB::table('mac_addresses')->insert($param);

        $param = [
            'mac_address' => '00:11:22:33:44:55',
            'vendor' => 'hoge.inc',
            'device_name' => 'すまほ-X',
            'user_id' => 2,
            'hide' => 0,
            'arraival_at' => $subHour,
            'departure_at' => $now->subHour(24),
            'posted_at' => $subSecond,
            'current_stay' => 1,
            'created_at' => $subDay,
            'updated_at' => $subSecond,
        ];
        DB::table('mac_addresses')->insert($param);

        $param = [
            'mac_address' => 'AA:BB:CC:33:44:55',
            'vendor' => 'fuga.inc',
            'device_name' => '非表示プリンタ',
            'user_id' => 2,
            'hide' => 1,
            'arraival_at' => $subHour,
            'departure_at' => $now->subHour(24),
            'posted_at' => $subSecond,
            'current_stay' => 1,
            'created_at' => $subDay,
            'updated_at' => $subSecond,
        ];
        DB::table('mac_addresses')->insert($param);

        $param = [
            'mac_address' => '00:11:22:DD:EE:FF',
            'vendor' => 'piyo.inc',
            'device_name' => 'i-piyo',
            'user_id' => 3,
            'hide' => 0,
            'arraival_at' => $subHour,
            'departure_at' => $now->subHour(24),
            'posted_at' => $now->subHour(12),
            'current_stay' => 0,
            'created_at' => $subDay,
            'updated_at' => $now->subHour(12),
        ];
        DB::table('mac_addresses')->insert($param);
    }
}
