<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Router>
 */
class RouterFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'community_id' => 1,
            'name' => $this->faker->name,
            'google_home_name' => $this->faker->name,
            'google_home_mac_address' => $this->faker->macAddress,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
