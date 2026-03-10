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
            'email' => 'test@example.com',
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
    }
}
