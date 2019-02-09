<?php

use App\UserTable;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(UserTable::class, function (Faker $faker) {
    return [
        'name' => $faker->name,
        'name_reading' => $faker->name,
        'unique_name' => $faker->safeEmail,
        'email' => $faker->email,
        'provisional' => 0,
        'password' => $faker->password,
        'created_at' => Carbon::now()->subDay(5),
        'updated_at'  => Carbon::yesterday(),
    ];
});

