<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\CoffeeShop>
 */
class CoffeeShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => $this->faker->company() . ' Coffee',
            'daerah' => $this->faker->city(),
            'kecamatan' => $this->faker->word(),
            'alamat' => $this->faker->address(),
            'jam_buka' => $this->faker->time('H:i:s'),
            'jam_tutup' => $this->faker->time('H:i:s'),
            'harga_min' => $this->faker->numberBetween(10000, 50000),
            'harga_max' => $this->faker->numberBetween(50000, 150000),
            'rating' => $this->faker->randomFloat(1, 0, 5),
            'deskripsi' => $this->faker->paragraph(),
            'kecamatan_id' => \App\Models\Kecamatan::factory(),
        ];
    }
}
