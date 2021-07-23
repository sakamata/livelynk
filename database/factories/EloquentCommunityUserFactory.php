<?php

use App\CommunityUser;
use App\Community;
use App\UserTable;
use Faker\Generator as Faker;

$factory->define(CommunityUser::class, function (Faker $faker) {
    return [
        'community_id' => factory(Community::class)->create()->id,
        'user_id' => factory(UserTable::class)->create()->id,
    ];
});
