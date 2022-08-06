<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\GlobalIp;
use Illuminate\Support\Facades\DB;

class GlobalIpTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'community_id' => 1,
            'name' => 'プレミアムラウンジ',
            'global_ip' => '192.168.1.1',
        ];
        GlobalIp::create($param);

        $param = [
            'community_id' => 1,
            'name' => 'コミュニティラウンジまたはフリーデスクスペース',
            'global_ip' => '192.168.1.2',
        ];
        GlobalIp::create($param);

        $param = [
            'community_id' => 2,
            'name' => 'ラウンジ',
            'global_ip' => '192.168.2.1',
        ];
        GlobalIp::create($param);

        $param = [
            'community_id' => 2,
            'name' => 'オフィス',
            'global_ip' => '192.168.2.2',
        ];
        GlobalIp::create($param);
    }
}
