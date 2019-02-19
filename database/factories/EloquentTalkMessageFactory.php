<?php

use App\TalkMessage;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(TalkMessage::class, function (Faker $faker) {
    return [
        'router_id' => 5,
        'talking_message' => $faker->realText(180),
        'created_at' => Carbon::now()->subSecond(60),
        'updated_at' => Carbon::now(),
    ];
});
