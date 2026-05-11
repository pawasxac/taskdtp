<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoffeeShopReview>
 */
class CoffeeShopReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'coffee_shop_id' => \App\Models\CoffeeShop::factory(),
            'rating' => $this->faker->numberBetween(1, 5),
            'review' => $this->faker->paragraph(),
        ];
    }
}
