<?php

namespace Database\Factories;

use App\CommunityUser;
use App\GlobalIp;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\MailBoxName>
 */
class MailBoxNameFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $faker_en = \Faker\Factory::create('en_US');

        return [
            'mail_box_name' => $faker_en->firstName,
            'community_user_id' => CommunityUser::factory()->create()->id,
            'global_ip_id' => GlobalIp::factory()->create()->id,
            'arraival_at' => Carbon::now()->subHours(2)->toDateTimeString(),
            'departure_at' => Carbon::now()->subMinutes(40)->toDateTimeString(),
            'posted_at' => Carbon::now()->subMinutes(5)->toDateTimeString(),
            'current_stay' => 1,
        ];
    }
}
