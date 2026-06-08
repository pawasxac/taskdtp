<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@example.com',
            'username' => 'testuser',
        ]);

        // Create Admin User
        User::create([
            'name' => 'Admin',
            'email' => 'admin@dailycoffee.com',
            'username' => 'admin',
            'password' => bcrypt('admin123'),
            'role' => 'admin',
        ]);

        // Seed the 18 real kecamatans in Sidoarjo
        $kecamatans = [
            'Sidoarjo', 'Buduran', 'Waru', 'Gedangan', 'Taman',
            'Krian', 'Sukodono', 'Candi', 'Tanggulangin', 'Porong',
            'Tulangan', 'Krembung', 'Jabon', 'Prambon', 'Tarik',
            'Balongbendo', 'Wonoayu', 'Sedati'
        ];
        foreach ($kecamatans as $name) {
            \App\Models\Kecamatan::firstOrCreate(['name' => $name]);
        }

        // Import data dari CSV
        $this->call(CoffeeShopDataSeeder::class);
    }
}
