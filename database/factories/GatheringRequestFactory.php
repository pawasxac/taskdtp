<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\GatheringRequest>
 */
class GatheringRequestFactory extends Factory
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
            'coffee_shop_id' => \App\Models\CoffeeShop::factory(),
            'requested_by' => \App\Models\User::factory(),
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'event_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
