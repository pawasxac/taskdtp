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

        // Generate Dummy Users for Forum and DM
        $dummyUsers = User::factory(15)->create();

        // Generate some DMs between Admin and User
        $admin = User::where('email', 'admin@kopi.com')->first();
        $user1 = User::where('email', 'user@kopi.com')->first();
        
        \App\Models\DirectMessage::create([
            'sender_id' => $user1->id,
            'receiver_id' => $admin->id,
            'message' => 'Bang, cafe di buduran yang colokannya banyak di mana ya?',
        ]);

        \App\Models\DirectMessage::create([
            'sender_id' => $admin->id,
            'receiver_id' => $user1->id,
            'message' => 'Coba ke circle kopi depan raya, aman sampe pagi.',
        ]);
        
        // Let's create some dummy DMs for user1 to test limit logic later
        for ($i=0; $i<8; $i++) {
            \App\Models\DirectMessage::create([
                'sender_id' => $user1->id,
                'receiver_id' => $admin->id,
                'message' => 'Spam test message ' . $i,
            ]);
        }
        
        // Generate Dummy Komunitas
        $komunitasNames = ['Pecinta Manual Brew Sidoarjo', 'Nugas Till Drop', 'Vibes Anak Senja Skena', 'Pemburu Colokan Waru'];
        
        foreach ($komunitasNames as $kName) {
            $komunitas = \App\Models\Komunitas::create([
                'nama_komunitas' => $kName,
                'deskripsi' => 'Komunitas resmi untuk anak skena yang suka kumpul dan bahas ' . $kName,
                'domisili' => $kecamatans[array_rand($kecamatans)],
                'ketua' => $dummyUsers->random()->name,
                'status' => 'aktif',
                'tanggal_dibentuk' => now(),
                'photo_url' => 'https://ui-avatars.com/api/?name=' . urlencode($kName) . '&background=C19A6B&color=1A0F0A&size=1200'
            ]);

            // Add leader
            $komunitas->members()->create([
                'user_id' => $dummyUsers->random()->id,
                'role' => 'leader'
            ]);

            // Add random members
            $members = $dummyUsers->random(rand(3, 8));
            foreach ($members as $mem) {
                $komunitas->members()->firstOrCreate(
                    ['user_id' => $mem->id],
                    ['role' => 'member']
                );
            }

            // Add dummy posts
            for ($j=0; $j<3; $j++) {
                $komunitas->posts()->create([
                    'user_id' => $dummyUsers->random()->id,
                    'content' => 'Ada rekomen kedai baru ga bro? Lagi pengen nyari suasana baru nih buat nugas.',
                ]);
            }
        }
    }
}
