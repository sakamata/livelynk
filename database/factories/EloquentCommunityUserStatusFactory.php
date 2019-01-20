<?php
use App\CommunityUserStatus;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(CommunityUserStatus::class, function (Faker $faker) {
    return [
        'id' => 1,
        'role_id' => 1,
        'hide' => 0,
        'last_access' => Carbon::now(),
        'created_at' => Carbon::now(),
        'updated_at' => Carbon::now(),
    ];
});
