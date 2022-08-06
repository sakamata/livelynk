<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MacAddressesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // tikara waza
        $now = Carbon::now();
        $subSecond = Carbon::now();
        $subSecond = $subSecond->subSecond(rand(1, 59));
        $subHour5 = Carbon::now();
        $subHour5 = $subHour5->subHour(rand(1, 5));
        $subHour = Carbon::now();
        $subHour = $subHour->subHour(rand(1, 23));
        $subDay = Carbon::now();
        $subDay = $subDay->subDay(rand(1, 59));

        $param = [
            'community_user_id' => 1,
            'router_id' => 1,
            'mac_address' => 'AA:BB:CC:DD:EE:FF',
            'mac_address_omission' => "AA:..:..:..:..:FF",
            'mac_address_hash' => $this->CahngeCrypt('AA:BB:CC:DD:EE:FF', 1),
            'vendor' => 'Apple.inc',
            'device_name' => 'i-phoneX',
            'hide' => 0,
            'arraival_at' => $subHour5,
            'departure_at' => $subHour,
            'posted_at' => $subSecond,
            'current_stay' => 1,
            'created_at' => $subDay,
            'updated_at' => $subSecond,
        ];
        DB::table('mac_addresses')->insert($param);

        $param = [
            'community_user_id' => 1,
            'router_id' => 2,
            'mac_address' => '00:11:22:33:44:55',
            'mac_address_omission' => "00:..:..:..:..:55",
            'mac_address_hash' => $this->CahngeCrypt('00:11:22:33:44:55', 1),
            'vendor' => 'hoge.inc',
            'device_name' => 'すまほ-X',
            'hide' => 0,
            'arraival_at' => $subHour5,
            'departure_at' => $subHour,
            'posted_at' => $subSecond,
            'current_stay' => 1,
            'created_at' => $subDay,
            'updated_at' => $subSecond,
        ];
        DB::table('mac_addresses')->insert($param);

        $param = [
            'community_user_id' => 1,
            'router_id' => 2,
            'mac_address' => 'AA:BB:CC:33:44:55',
            'mac_address_omission' => "AA:..:..:..:..:55",
            'mac_address_hash' => $this->CahngeCrypt('AA:BB:CC:33:44:55', 1),
            'vendor' => 'fuga.inc',
            'device_name' => '非表示プリンタ',
            'hide' => 1,
            'arraival_at' => $subHour5,
            'departure_at' => $subHour,
            'posted_at' => $subSecond,
            'current_stay' => 1,
            'created_at' => $subDay,
            'updated_at' => $subSecond,
        ];
        DB::table('mac_addresses')->insert($param);

        $param = [
            'community_user_id' => 5,
            'router_id' => 2,
            'mac_address' => '00:11:22:DD:EE:FF',
            'mac_address_omission' => "00:..:..:..:..:FF",
            'mac_address_hash' => $this->CahngeCrypt('00:11:22:DD:EE:FF', 1),
            'vendor' => 'piyo.inc',
            'device_name' => 'i-piyo',
            'hide' => 0,
            'arraival_at' => $subHour5,
            'departure_at' => $subHour,
            'posted_at' => $subSecond,
            'current_stay' => 1,
            'created_at' => $subDay,
            'updated_at' => $subSecond,
        ];
        DB::table('mac_addresses')->insert($param);

        // community1 前半 滞在中
        for ($i=5; $i <= 8; $i++) {
            $now = Carbon::now();
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1, 600));
            $subMinutes = Carbon::now();
            $subMinutes = $subMinutes->subMinutes(rand(11, 59));
            $subHour5 = Carbon::now();
            $subHour5 = $subHour5->subHour(rand(1, 5));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1, 23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(1, 59));

            $rand_router = rand(1, 2);
            $posted = Carbon::now();
            $posted = $posted->subSecond(rand(1, 59));

            $stay = 1;
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $i,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 1),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => $subMinutes,
                'departure_at' => $subHour,
                'posted_at' => $subSecond,
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $subHour,
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // community1 後半 帰宅中
        for ($i=9; $i <= 12; $i++) {
            $now = Carbon::now();
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1800, 40000));
            $subMinutes = Carbon::now();
            $subMinutes = $subMinutes->subMinutes(rand(1, 59));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(24, 48));
            $subHour2 = Carbon::now();
            $subHour2 = $subHour2->subHour(rand(12, 23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(1, 59));

            $rand_router = rand(1, 2);
            $posted = Carbon::now();
            $posted = $posted->subSecond(rand(1, 59));

            $stay = 0;
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $i,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 1),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => $subHour,
                'departure_at' => $subHour2,
                'posted_at' => $subSecond,
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $subHour,
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // community2
        for ($i=13; $i <= 21; $i++) {
            $now = Carbon::now();
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1, 59));
            $subMinutes = Carbon::now();
            $subMinutes = $subMinutes->subMinutes(rand(1, 59));
            $subHour5 = Carbon::now();
            $subHour5 = $subHour5->subHour(rand(1, 5));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1, 23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(1, 59));

            $rand_router = rand(3, 4);
            $posted = Carbon::now();
            $posted = $posted->subSecond(rand(1, 59));

            $posted30 = Carbon::now();
            $posted30 = $posted30->subSecond(30);
            if ($posted < $posted30) {
                $stay = 1;
            } else {
                $stay = 0;
            }
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $i,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 2),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => $subMinutes,
                'departure_at' => $subHour,
                'posted_at' => $subSecond,
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $subHour,
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // community3
        for ($i=22; $i <= 30; $i++) {
            $now = Carbon::now();
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1, 59));
            $subMinutes = Carbon::now();
            $subMinutes = $subMinutes->subMinutes(rand(1, 59));
            $subHour5 = Carbon::now();
            $subHour5 = $subHour5->subHour(rand(1, 5));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1, 23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(1, 59));

            $rand_router = 5;
            $posted = Carbon::now();
            $posted = $posted->subSecond(rand(1, 59));

            $posted30 = Carbon::now();
            $posted30 = $posted30->subSecond(30);
            if ($posted < $posted30) {
                $stay = 1;
            } else {
                $stay = 0;
            }
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $i,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 3),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => $subMinutes,
                'departure_at' => $subHour,
                'posted_at' => $subSecond,
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $subHour,
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // community2 重複ユーザー
        for ($i=31; $i <= 36; $i++) {
            $now = Carbon::now();
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1, 59));
            $subMinutes = Carbon::now();
            $subMinutes = $subMinutes->subMinutes(rand(1, 59));
            $subHour5 = Carbon::now();
            $subHour5 = $subHour5->subHour(rand(1, 5));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1, 23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(1, 59));

            $rand_router = rand(3, 4);
            $posted = Carbon::now();
            $posted = $posted->subSecond(rand(1, 59));

            $posted30 = Carbon::now();
            $posted30 = $posted30->subSecond(30);
            if ($posted < $posted30) {
                $stay = 1;
            } else {
                $stay = 0;
            }
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $i,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 2),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => $subMinutes,
                'departure_at' => $subHour,
                'posted_at' => $subSecond,
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $subHour,
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // community3 重複ユーザー
        for ($i=37; $i <= 40; $i++) {
            $now = Carbon::now();
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1, 59));
            $subMinutes = Carbon::now();
            $subMinutes = $subMinutes->subMinutes(rand(1, 59));
            $subHour5 = Carbon::now();
            $subHour5 = $subHour5->subHour(rand(1, 5));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1, 23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(1, 59));

            $rand_router = 5;
            $posted = Carbon::now();
            $posted = $posted->subSecond(rand(1, 59));

            $posted30 = Carbon::now();
            $posted30 = $posted30->subSecond(30);
            if ($posted < $posted30) {
                $stay = 1;
            } else {
                $stay = 0;
            }
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $i,
                'router_id' => $rand_router,
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 3),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => $subMinutes,
                'departure_at' => $subHour,
                'posted_at' => $subSecond,
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $subHour,
            ];
            DB::table('mac_addresses')->insert($param);
        }

        // provision user用
        $id = 41;
        $router_id = array(1,2,3,4,5,5);
        for ($i=0; $i <=5; $i++) {
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $id,
                'router_id' => $router_id[$i],
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 1),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => Carbon::now(),
                'departure_at' => null,
                'posted_at' => Carbon::now(),
                'current_stay' => 1,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
            DB::table('mac_addresses')->insert($param);
            $id++;
        }

        // community4
        for ($i=47; $i <= 56; $i++) {
            $now = Carbon::now();
            $subSecond = Carbon::now();
            $subSecond = $subSecond->subSecond(rand(1, 59));
            $subMinutes = Carbon::now();
            $subMinutes = $subMinutes->subMinutes(rand(1, 59));
            $subHour5 = Carbon::now();
            $subHour5 = $subHour5->subHour(rand(1, 5));
            $subHour = Carbon::now();
            $subHour = $subHour->subHour(rand(1, 23));
            $subDay = Carbon::now();
            $subDay = $subDay->subDay(rand(1, 59));

            $posted = Carbon::now();
            $posted = $posted->subSecond(rand(1, 59));

            $posted30 = Carbon::now();
            $posted30 = $posted30->subSecond(30);
            if ($posted < $posted30) {
                $stay = 1;
            } else {
                $stay = 0;
            }
            $mac_top = strtoupper(str_random(2)) ;
            $mac_bottom = strtoupper(str_random(2)) ;
            $mac = $mac_top .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. strtoupper(str_random(2)) .':'. $mac_bottom;
            $XX = strtoupper(str_random(2));
            $vendor = $XX . $XX . $XX . '.inc';
            $device = $XX . $XX . $XX . $XX;
            $param = [
                'community_user_id' => $i,
                'router_id' => 6,
                'mac_address' => $mac,
                'mac_address_omission' => $mac_top .":..:..:..:..:" . $mac_bottom,
                'mac_address_hash' => $this->CahngeCrypt($mac, 3),
                'vendor' => $vendor,
                'device_name' => $device,
                'hide' => 0,
                'arraival_at' => $subMinutes,
                'departure_at' => $subHour,
                'posted_at' => $subSecond,
                'current_stay' => $stay,
                'created_at' => $subDay,
                'updated_at' => $subHour,
            ];
            DB::table('mac_addresses')->insert($param);
        }
    }

    public function CahngeCrypt($mac_address, $community_id)
    {
        $secret = DB::table('communities')->where('id', $community_id)
            ->pluck('hash_key')->first();
        return hash('sha256', $mac_address . $secret);
    }
}
