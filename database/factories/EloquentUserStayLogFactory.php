<?php
use App\UserStayLog;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(UserStayLog::class, function (Faker $faker) {
    return [
        'community_user_id' => 1,
        'arraival_at' => Carbon::now()->subHour(2),
        'departure_at' => Carbon::now()->subHour(1),
        'last_datetime' => Carbon::now()->subMinutes(2),
        'created_at' => Carbon::now()->subHour(2),
        'updated_at' => Carbon::now()->subMinutes(2),
    ];
});
