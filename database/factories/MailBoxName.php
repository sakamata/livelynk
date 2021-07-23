<?php

use Faker\Generator as Faker;
use App\CommunityUser;
use App\MailBoxName;
use App\GlobalIp;
use Carbon\Carbon;

$factory->define(MailBoxName::class, function (Faker $faker) {
    $faker_en = \Faker\Factory::create('en_US');
    return [
        'mail_box_name' => $faker_en->firstName,
        'community_user_id' => factory(CommunityUser::class)->create()->id,
        'global_ip_id' => factory(GlobalIp::class)->create()->id,
        'arraival_at' => Carbon::now()->subHours(2)->toDateTimeString(),
        'departure_at' => Carbon::now()->subMinutes(40)->toDateTimeString(),
        'posted_at' => Carbon::now()->subMinutes(5)->toDateTimeString(),
        'current_stay' => 1,
    ];
});
