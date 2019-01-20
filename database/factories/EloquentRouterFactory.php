<?php

use App\Router;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Router::class, function (Faker $faker) {
    return [
        'community_id' => 1,
        'name' => $faker->name,
        'google_home_name' => $faker->name,
        'google_home_mac_address' => $faker->macAddress,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
