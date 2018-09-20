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
            'community_id' => 1,
            'router_id' => 1,
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
            'community_id' => 1,
            'router_id' => 2,
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
            'community_id' => 2,
            'router_id' => 3,
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
            'community_id' => 3,
            'router_id' => 5,
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

        // community1 ダミー入れるのに力入れ過ぎた…
        for ($i=0; $i < 10; $i++) {
            $rand_router = rand(1,2);
            $stay = rand(0,1);
            $mac = strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2));
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $id = array("1","5","6");
            $key = array_rand($id, 1);
            $user_id = $id[$key];
            $param = [
                'community_id' => 1,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'vendor' => $vendor,
                'device_name' => $device,
                'user_id' => $user_id,
                'hide' => 0,
                'arraival_at' => $subHour,
                'departure_at' => $now->subHour(24),
                'posted_at' => $now->subHour(12),
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $now->subHour(12),
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // community2
        for ($i=0; $i < 10; $i++) {
            $rand_router = rand(3,4);
            $stay = rand(0,1);
            $mac = strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2));
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $id = array("2","4","7");
            $key = array_rand($id, 1);
            $user_id = $id[$key];
            $param = [
                'community_id' => 2,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'vendor' => $vendor,
                'device_name' => $device,
                'user_id' => $user_id,
                'hide' => 0,
                'arraival_at' => $subHour,
                'departure_at' => $now->subHour(24),
                'posted_at' => $now->subHour(12),
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $now->subHour(12),
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // community3
        for ($i=0; $i < 10; $i++) {
            $rand_router = 5;
            $stay = rand(0,1);
            $mac = strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2));
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $id = array("8","9","10");
            $key = array_rand($id, 1);
            $user_id = $id[$key];
            $param = [
                'community_id' => 3,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'vendor' => $vendor,
                'device_name' => $device,
                'user_id' => $user_id,
                'hide' => 0,
                'arraival_at' => $subHour,
                'departure_at' => $now->subHour(24),
                'posted_at' => $now->subHour(12),
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $now->subHour(12),
            ];
            DB::table('mac_addresses')->insert($param);
        }


    }
}
