<?php
use App\Community;
use App\UserTable;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(Community::class, function (Faker $faker) {
    return [
        'enable' => true,
        'user_id' => factory(UserTable::class)->create()->id,
        'name' => $faker->country,
        'service_name' => $faker->company,
        'service_name_reading' => $faker->company,
        'url_path' => $faker->password,
        'hash_key' => $faker->password,
        'mail_box_domain' => $faker->safeEmailDomain,
        'tumolink_enable' => false,
        'calendar_enable' => false,
        'calendar_public_iframe' => null,
        'calendar_secret_iframe' => null,
        'google_home_enable' => false,
        'admin_comment' => null,
        'created_at' => Carbon::now()->subDay(5),
        'updated_at' => Carbon::now()->subDay(3),
    ];
});
