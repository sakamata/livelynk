<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\UserStayLog>
 */
class UserStayLogFactory extends Factory
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
            'arraival_at' => Carbon::now()->subHour(2),
            'departure_at' => Carbon::now()->subHour(1),
            'last_datetime' => Carbon::now()->subMinutes(2),
            'created_at' => Carbon::now()->subHour(2),
            'updated_at' => Carbon::now()->subMinutes(2),
        ];
    }
}
