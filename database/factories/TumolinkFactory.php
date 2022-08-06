<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Tumolink>
 */
class TumolinkFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'community_user_id' => 1,
            'maybe_arraival' => Carbon::now()->addHour(1),
            'maybe_departure' => Carbon::now()->addHour(1),
            'google_home_push' => 1,
            'created_at' => Carbon::now()->subDay(5),
            'updated_at' => Carbon::now(),
        ];
    }
}
