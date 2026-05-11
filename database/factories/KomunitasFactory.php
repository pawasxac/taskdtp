<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Komunitas>
 */
class KomunitasFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama_komunitas' => $this->faker->company() . ' Community',
            'domisili' => $this->faker->city(),
            'ketua' => $this->faker->name(),
            'deskripsi' => $this->faker->paragraph(),
            'tanggal_dibentuk' => $this->faker->date(),
            'jumlah_anggota' => $this->faker->numberBetween(10, 100),
            'kontak' => $this->faker->phoneNumber(),
            'status' => $this->faker->randomElement(['aktif', 'nonaktif']),
        ];
    }
}
