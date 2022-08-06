<?php

namespace Database\Factories;

use App\UserTable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Community>
 */
class CommunityFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'enable' => true,
            'user_id' => UserTable::factory()->create()->id,
            'name' => $this->faker->country,
            'service_name' => $this->faker->company,
            'service_name_reading' => $this->faker->company,
            'url_path' => Str::random(32),
            'hash_key' => Str::random(32),
            'mail_box_domain' => $this->faker->safeEmailDomain,
            'tumolink_enable' => false,
            'calendar_enable' => false,
            'calendar_public_iframe' => null,
            'calendar_secret_iframe' => null,
            'google_home_enable' => false,
            'admin_comment' => null,
            'created_at' => Carbon::now()->subDay(5),
            'updated_at' => Carbon::now()->subDay(3),
        ];
    }
}
