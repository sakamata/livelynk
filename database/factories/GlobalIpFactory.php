<?php

namespace Database\Factories;

use App\Community;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\GlobalIp>
 */
class GlobalIpFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $ips = ['::ffff:'. $this->faker->ipv4, $this->faker->ipv6];
        return [
            'community_id' => Community::factory()->create()->id,
            'global_ip' => $ips[array_rand($ips)],
        ];
    }
}
