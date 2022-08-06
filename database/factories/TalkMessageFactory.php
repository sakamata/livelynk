<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\TalkMessage>
 */
class TalkMessageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'router_id' => 5,
            'talking_message' => $this->faker->realText(180),
            'created_at' => Carbon::now()->subSecond(60),
            'updated_at' => Carbon::now(),
        ];
    }
}
