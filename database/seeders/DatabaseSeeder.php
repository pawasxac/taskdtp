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

        // Seed dummy data for all tables
        \App\Models\Kecamatan::factory(5)->create();
        \App\Models\CoffeeShop::factory(5)->create();
        \App\Models\CoffeeShopReview::factory(5)->create();
        \App\Models\Komunitas::factory(5)->create();
        \App\Models\CommunityMember::factory(5)->create();
        \App\Models\GatheringRequest::factory(5)->create();
        \App\Models\CommunityPost::factory(5)->create();
        \App\Models\CommunityComment::factory(5)->create();
        \App\Models\LoginLog::factory(5)->create();
    }
}
