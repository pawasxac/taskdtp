<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CommunityComment>
 */
class CommunityCommentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'post_id' => \App\Models\CommunityPost::factory(),
            'user_id' => \App\Models\User::factory(),
            'comment' => $this->faker->sentence(),
        ];
    }
}
