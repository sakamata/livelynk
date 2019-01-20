<?php

use App\CommunityUser;
use Faker\Generator as Faker;

$factory->define(CommunityUser::class, function (Faker $faker) {
    return [
        'community_id' => 1,
        'user_id' => 1,
    ];
});
