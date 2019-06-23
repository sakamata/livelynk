<?php

use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserStayLogTableSeeder extends Seeder
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

        $subMinute = Carbon::now();
        $subMinute = $subMinute->subSecond(60);
        $subMinute5 = Carbon::now();
        $subMinute5 = $subMinute5->subSecond(60 * 5);
        $subMinute20 = Carbon::now();
        $subMinute20 = $subMinute20->subSecond(60 * 20);

        $subHour = Carbon::now();
        $subHour = $subHour->subHour(1);
        $subDay = Carbon::now();
        $subDay = $subDay->subDay(1);

        $param = [
            'community_user_id' => 1,
            'arraival_at' => $subHour,
            'departure_at' => null,
            'last_datetime' => $subHour,
            'created_at' => $subHour,
            'updated_at' => $subMinute20,
        ];
        DB::table('users_stays_logs')->insert($param);

    }
}
