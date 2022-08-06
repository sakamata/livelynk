<?php

namespace Database\Factories;

use App\Community;
use App\UserTable;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\CommunityUser>
 */
class CommunityUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'community_id' => Community::factory()->create()->id,
            'user_id' => UserTable::factory()->create()->id,
        ];
    }
}
