<?php

use App\Community;
use App\GlobalIp;
use Faker\Generator as Faker;

$factory->define(GlobalIp::class, function (Faker $faker) {
    $ips = ['::ffff:'.$faker->ipv4, $faker->ipv6];
    return [
        'community_id' => factory(Community::class)->create()->id,
        'global_ip' => $ips[array_rand($ips)],
    ];
});
