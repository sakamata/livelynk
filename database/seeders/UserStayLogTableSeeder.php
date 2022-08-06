<?php

namespace Database\Seeders;

use App\UserStayLog;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserStayLogTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now           = Carbon::now();
        $p1M       = Carbon::now()->subMinute(1);
        $p2M       = Carbon::now()->subMinute(2);
        $p3M       = Carbon::now()->subMinute(3);
        $p60M      = Carbon::now()->subHour();
        $p89M      = Carbon::now()->subMinute(89);
        $p90M      = Carbon::now()->subMinute(90);
        $p90M      = Carbon::now()->subMinute(90);
        $p2H      = Carbon::now()->subHours(2);
        $p8H      = Carbon::now()->subHours(8);
        $p12H     = Carbon::now()->subHours(12);
        $p1D       = Carbon::now()->subHours(24);
        $p2D       = Carbon::now()->subHours(48);
        $p3D       = Carbon::now()->subHours(72);

        $arr = [
            ['id' => 1,   'arrai' => $p3D,    'dep' => $p2D,      'last' => $p2D],
            ['id' => 2,   'arrai' => $p2D,    'dep' => $p1D,      'last' => $p1D],
            ['id' => 1,   'arrai' => $p1D,    'dep' => null,      'last' => $p1M],
            ['id' => 2,   'arrai' => $p12H,   'dep' => null,      'last' => $p1M],
            ['id' => 5,   'arrai' => $p8H,    'dep' => null,      'last' => $p2M],
            ['id' => 6,   'arrai' => $p2H,    'dep' => null,      'last' => $p3M],
            ['id' => 7,   'arrai' => $p90M,   'dep' => null,      'last' => $p60M],
            ['id' => 8,   'arrai' => $p89M,   'dep' => null,      'last' => $p89M],
        ];

        $i= 0;
        foreach ($arr as $key => $value) {
            UserStayLog::factory()->create([
                'community_user_id' => $value['id'],
                'arraival_at' => $value['arrai'],
                'departure_at' => $value['dep'],
                'last_datetime' => $value['last'],
            ]);
            $i++;
        }
    }
}
