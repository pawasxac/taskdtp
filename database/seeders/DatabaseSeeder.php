<?php

namespace Database\Seeders;

use App\Models\Kecamatan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database with the canonical
     * Roastery Skena testing accounts and structural dictionary.
     *
     * The `role` column is a primitive string column ('admin' | 'user').
     * Passwords are encrypted via Laravel's Hash facade so they match
     * the verification path used by `auth()->attempt()`.
     *
     * Every tester account is connected to the 180 coffee shop dataset
     * (latitude, longitude, kecamatan, operating hours) by simply
     * sharing the same `kecamatans` table and the related `users`
     * profile that the app reads through Eloquent relations.
     */
    public function run(): void
    {
        /**
         * Canonical tester accounts.
         * - email    : login identity
         * - password : raw string, immediately encrypted via Hash::make
         * - role     : primitive string used for strict comparison
         */
        $accounts = [
            [
                'email'    => 'admin@kopi.com',
                'password' => 'password',
                'name'     => 'Admin Roastery',
                'username' => 'adminroastery',
                'role'     => 'admin',
                'bio'      => 'Penjaga gawang Roastery Skena. Audit, validasi, dan bantu anak skena nemuin meja paling enak buat nugas malem-malem.',
                'phone'    => '081200000001',
            ],
            [
                'email'    => 'user@kopi.com',
                'password' => 'password',
                'name'     => 'Anak Skena Mbois',
                'username' => 'anakskena',
                'role'     => 'user',
                'bio'      => 'Nugas, nyeruput, ngobrol. Anak skena yang nemuin spot lewat Roastery Skena, dari manual brew sampai meja pojok.',
                'phone'    => '081200000002',
            ],
        ];

        foreach ($accounts as $account) {
            User::updateOrCreate(
                ['email' => $account['email']],
                [
                    'name'              => $account['name'],
                    'username'          => $account['username'],
                    'password'          => Hash::make($account['password']),
                    'role'              => $account['role'],
                    'bio'               => $account['bio'],
                    'phone_number'      => $account['phone'],
                    'profile_picture'   => 'https://ui-avatars.com/api/?name='
                        . urlencode($account['name'])
                        . '&background=1A0F0A&color=FAF6F0&bold=true',
                    'email_verified_at' => now(),
                ]
            );
        }

        /**
         * Kecamatan dictionary — these names mirror the `kecamatan`
         * column on the 180 coffee shop CSV so the relational bridge
         * (`kecamatan_id`) and the legacy string column stay in sync.
         */
        $kecamatans = [
            'Sidoarjo', 'Buduran', 'Waru', 'Gedangan', 'Taman',
            'Krian', 'Sukodono', 'Candi', 'Tanggulangin', 'Porong',
            'Tulangan', 'Krembung', 'Jabon', 'Prambon', 'Tarik',
            'Balongbendo', 'Wonoayu', 'Sedati',
        ];

        foreach ($kecamatans as $name) {
            Kecamatan::firstOrCreate(['name' => $name]);
        }

        /**
         * Hand off to the CSV-driven seeder that materialises the
         * 180 coffee shop rows (latitude, longitude, jam_buka,
         * jam_tutup, kecamatan) into the database.
         */
        $this->call(CoffeeShopDataSeeder::class);
    }
}
