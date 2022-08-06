<?php

namespace Database\Factories;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\UserTable>
 */
class UserTableFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'name_reading' => $this->faker->name,
            'unique_name' => $this->faker->unique()->userName,
            'email' => $this->faker->unique()->email,
            'provisional' => 0,
            'password' => bcrypt('aaaaaa'),
            'created_at' => Carbon::now()->subDay(5),
            'updated_at'  => Carbon::yesterday(),
        ];
    }
}
