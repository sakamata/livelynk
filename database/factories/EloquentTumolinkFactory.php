<?php

use App\Tumolink;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Tumolink::class, function (Faker $faker) {
    return [
        'community_user_id' => 1,
        'maybe_arraival' => Carbon::now()->addHour(1),
        'maybe_departure' => Carbon::now()->addHour(1),
        'google_home_push' => 1,
        'created_at' => Carbon::now()->subDay(5),
        'updated_at' => Carbon::now(),
    ];
});
