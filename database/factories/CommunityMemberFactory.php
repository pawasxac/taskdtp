<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommunityMember>
 */
class CommunityMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'community_id' => \App\Models\Komunitas::factory(),
            'user_id' => \App\Models\User::factory(),
            'role' => $this->faker->randomElement(['leader', 'member']),
            'joined_at' => $this->faker->dateTime(),
        ];
    }
}
