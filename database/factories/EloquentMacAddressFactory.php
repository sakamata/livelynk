<?php

use App\MacAddress;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(MacAddress::class, function (Faker $faker) {
    return [
        'mac_address' => $faker->macAddress,
        'mac_address_hash' => $faker->sha256,
        'vendor' => $faker->company,
        'device_name' => $faker->company,
        'community_user_id' => 1,
        'hide' => 0,
        'router_id' => 1,
        'arraival_at' => Carbon::now(),
        'departure_at' => Carbon::now(),
        'posted_at' => Carbon::now(),
        'posted_at' => Carbon::now(),
        'current_stay' => 1,
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
