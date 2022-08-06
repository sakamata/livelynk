<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\MacAddress>
 */
class MacAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $macAddress = $this->faker->macAddress;
        $front = substr($macAddress, 0, 2);
        $bottom = substr($macAddress, -2);

        return [
            'mac_address' => $macAddress,
            'mac_address_omission' => $front . ":..:..:..:..:" . $bottom,
            'mac_address_hash' => $this->faker->sha256,
            'vendor' => $this->faker->company,
            'device_name' => $this->faker->company,
            'community_user_id' => 1,
            'hide' => 0,
            'router_id' => 1,
            'arraival_at' => Carbon::now(),
            'departure_at' => Carbon::now(),
            'posted_at' => Carbon::now(),
            'current_stay' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];
    }
}
