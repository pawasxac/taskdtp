<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        $userIds   = DB::table('users')->pluck('id')->toArray();
        $shopIds   = DB::table('coffee_shops')->pluck('id')->toArray();

        if (empty($userIds)) {
            $this->command->error('Tidak ada user di database. Tambahkan user dulu.');
            return;
        }

        // ─────────────────────────────────────────────
        // 1. KOMUNITAS
        // ─────────────────────────────────────────────
        $this->command->info('Seeding komunitas...');

        $komunitasList = [
            ['nama_komunitas' => 'Sidoarjo Coffee Circle', 'domisili' => 'Sidoarjo', 'ketua' => 'Budi Santoso', 'deskripsi' => 'Komunitas pecinta kopi di Sidoarjo yang aktif berbagi informasi kedai kopi terbaik, tips brewing, dan ngumpul bareng setiap minggu.', 'kontak' => '08123456789'],
            ['nama_komunitas' => 'Ngopi Waru Geng', 'domisili' => 'Waru', 'ketua' => 'Siti Aminah', 'deskripsi' => 'Kumpulan anak nongkrong wilayah Waru yang doyan eksplorasi manual brew dan diskusi santai sambil ngupi.', 'kontak' => '08234567890'],
            ['nama_komunitas' => 'Krian Kopi Crew', 'domisili' => 'Krian', 'ketua' => 'Andi Firmansyah', 'deskripsi' => 'Komunitas ngopi Krian dengan anggota aktif yang rutin gathering dan review kedai kopi baru setiap bulannya.', 'kontak' => '08345678901'],
            ['nama_komunitas' => 'Porong Brew Society', 'domisili' => 'Porong', 'ketua' => 'Dewi Rahayu', 'deskripsi' => 'Pecinta kopi wilayah Porong yang suka diskusi soal biji kopi, metode seduh, dan spot nongkrong yang cozy.', 'kontak' => '08456789012'],
            ['nama_komunitas' => 'Gedangan Caffeine Club', 'domisili' => 'Gedangan', 'ketua' => 'Riko Prasetyo', 'deskripsi' => 'Komunitas yang misi utamanya mencicipi semua kedai kopi di Gedangan dan sekitarnya, lalu bikin review jujur.', 'kontak' => '08567890123'],
            ['nama_komunitas' => 'Taman Coffee Lovers', 'domisili' => 'Taman', 'ketua' => 'Lina Kusuma', 'deskripsi' => 'Komunitas ngopi santai Taman Sidoarjo, terbuka untuk semua kalangan yang mau gabung nongkrong bareng.', 'kontak' => '08678901234'],
            ['nama_komunitas' => 'Wonoayu Kopi Community', 'domisili' => 'Wonoayu', 'ketua' => 'Hendra Wijaya', 'deskripsi' => 'Komunitas pecinta kopi Wonoayu yang gemar berbagi rekomendasi kedai dan tips memilih kopi berkualitas.', 'kontak' => '08789012345'],
            ['nama_komunitas' => 'Buduran Sip Circle', 'domisili' => 'Buduran', 'ketua' => 'Maya Sari', 'deskripsi' => 'Circle ngopi Buduran, aktif di media sosial dan rutin mengadakan acara cupping bersama setiap bulan.', 'kontak' => '08890123456'],
        ];

        $komunitasIds = [];
        foreach ($komunitasList as $k) {
            $id = DB::table('komunitas')->insertGetId([
                'nama_komunitas'  => $k['nama_komunitas'],
                'domisili'        => $k['domisili'],
                'ketua'           => $k['ketua'],
                'deskripsi'       => $k['deskripsi'],
                'tanggal_dibentuk'=> Carbon::now()->subMonths(rand(6, 24))->toDateString(),
                'jumlah_anggota'  => rand(10, 80),
                'kontak'          => $k['kontak'],
                'status'          => 'aktif',
                'created_at'      => Carbon::now()->subMonths(rand(3, 18)),
                'updated_at'      => Carbon::now(),
            ]);
            $komunitasIds[] = $id;
        }
        $this->command->info('✓ ' . count($komunitasIds) . ' komunitas ditambahkan.');

        // ─────────────────────────────────────────────
        // 2. COMMUNITY MEMBERS
        // ─────────────────────────────────────────────
        $this->command->info('Seeding community_members...');

        $memberInserted = 0;
        foreach ($komunitasIds as $komId) {
            // Setiap komunitas dapat semua user sebagai anggota
            $role = 'leader';
            foreach ($userIds as $uid) {
                DB::table('community_members')->insertOrIgnore([
                    'community_id' => $komId,
                    'user_id'      => $uid,
                    'role'         => $role,
                    'joined_at'    => Carbon::now()->subDays(rand(10, 300)),
                    'created_at'   => Carbon::now(),
                    'updated_at'   => Carbon::now(),
                ]);
                $role = 'member';
                $memberInserted++;
            }
        }
        $this->command->info("✓ {$memberInserted} community_members ditambahkan.");

        // ─────────────────────────────────────────────
        // 3. COMMUNITY POSTS
        // ─────────────────────────────────────────────
        $this->command->info('Seeding community_posts...');

        $postContents = [
            'Ada yang tahu kedai kopi baru yang buka di area sini? Pengen eksplorasi akhir pekan ini!',
            'Baru nyoba cold brew di beberapa tempat, ternyata yang paling enak yang dekat alun-alun. Wajib coba!',
            'Tips nugas di kafe: cari yang WiFi-nya kencang, kursinya empuk, dan baristanya nggak bawel kalau kita duduk lama.',
            'Manual brew memang butuh waktu lebih lama, tapi rasanya beda banget dibanding drip biasa. Worth it!',
            'Ada yang pernah nyoba kopi Sidoarjo single origin? Ternyata ada lho yang jual beans lokal di sini.',
            'Gathering bulan depan gimana? Rencana mau ke kedai yang belum pernah kita datangi bareng.',
            'Rekomendasi kafe yang cozy buat kerja sendiri tanpa diusir meskipun lama? Butuh banget nih.',
            'Biji kopi dari mana yang paling kalian suka? Personally aku suka yang dari Flores, asamnya fruity banget.',
            'Akhirnya nemuin spot nongkrong yang enak banget buat diskusi, bisa sambil dengerin live music lagi!',
            'Siapa yang mau ikut coffee tour keliling Sidoarjo bulan ini? Rencananya mau coba 5 kedai sekaligus.',
            'Pour over vs V60, mana yang lebih kalian suka? Di sini kayaknya banyak yang pilih V60.',
            'Kafe yang ada outdoor seating-nya itu buat aku lebih nyaman, bisa ngerokok juga kalau mau.',
        ];

        $postInserted = 0;
        foreach ($komunitasIds as $komId) {
            $numPosts = rand(3, 5);
            for ($p = 0; $p < $numPosts; $p++) {
                DB::table('community_posts')->insert([
                    'community_id' => $komId,
                    'user_id'      => $userIds[array_rand($userIds)],
                    'content'      => $postContents[array_rand($postContents)],
                    'created_at'   => Carbon::now()->subDays(rand(1, 60)),
                    'updated_at'   => Carbon::now(),
                ]);
                $postInserted++;
            }
        }
        $this->command->info("✓ {$postInserted} community_posts ditambahkan.");

        // ─────────────────────────────────────────────
        // 4. GATHERING REQUESTS
        // ─────────────────────────────────────────────
        $this->command->info('Seeding gathering_requests...');

        $gatheringTitles = [
            'Ngopi Bareng Sabtu Sore',
            'Coffee Meetup Akhir Bulan',
            'Mini Cupping Session',
            'Eksplorasi Kedai Baru',
            'Gathering Rutin Bulanan',
            'Diskusi Manual Brew',
            'Coffee & Talk Santai',
            'Hunting Kopi Akhir Pekan',
        ];
        $gatheringDescs = [
            'Gathering santai sambil ngobrol soal kopi dan kehidupan. Semua anggota diundang!',
            'Eksplorasi kedai kopi baru yang belum pernah kita kunjungi bersama. Yuk merapat!',
            'Session cupping bersama untuk membandingkan berbagai jenis biji kopi yang ada.',
            'Ngumpul, ngobrol, dan tentunya ngopi bareng. Dresscode bebas, yang penting santai.',
        ];
        $statuses = ['pending', 'pending', 'approved', 'approved', 'rejected'];

        $gatherInserted = 0;
        foreach ($komunitasIds as $komId) {
            $numGatherings = rand(1, 2);
            for ($g = 0; $g < $numGatherings; $g++) {
                DB::table('gathering_requests')->insert([
                    'community_id'  => $komId,
                    'coffee_shop_id'=> $shopIds[array_rand($shopIds)],
                    'requested_by'  => $userIds[array_rand($userIds)],
                    'title'         => $gatheringTitles[array_rand($gatheringTitles)],
                    'description'   => $gatheringDescs[array_rand($gatheringDescs)],
                    'event_date'    => Carbon::now()->addDays(rand(3, 45))->toDateString(),
                    'status'        => $statuses[array_rand($statuses)],
                    'created_at'    => Carbon::now()->subDays(rand(1, 30)),
                    'updated_at'    => Carbon::now(),
                ]);
                $gatherInserted++;
            }
        }
        $this->command->info("✓ {$gatherInserted} gathering_requests ditambahkan.");

        // ─────────────────────────────────────────────
        // 5. COFFEE SHOP REVIEWS
        // ─────────────────────────────────────────────
        $this->command->info('Seeding coffee_shop_reviews...');

        $reviewTexts = [
            'Tempatnya nyaman banget buat nugas, WiFi kencang dan kursinya empuk. Kopinya juga enak!',
            'Barista-nya ramah dan mau jelaskan proses brewing-nya. Pengalaman ngopi yang berkesan.',
            'Harganya reasonable untuk kualitas kopi yang didapat. Akan balik lagi pastinya.',
            'Suasananya cozy dan musiknya pas banget, nggak terlalu keras. Cocok buat kerja atau santai.',
            'Kopinya mantap, apalagi yang single origin-nya. Asamnya fruity dan aftertaste-nya panjang.',
            'Agak ramai di akhir pekan tapi servis tetap cepat. Kopi late-nya creamy banget.',
            'Tempat parkirnya luas dan lokasinya strategis. Jadi favorit buat meeting santai.',
            'Cold brew-nya salah satu yang terbaik yang pernah saya coba di Sidoarjo.',
            'Interior-nya aesthetic, bagus buat foto tapi yang lebih penting kopinya memang enak.',
            'Jam buka cukup lama jadi bisa ngopi sambil ngerjain deadline malam-malam.',
        ];

        // Pilih 50 coffee shop acak, masing-masing dapat 1 review dari user berbeda
        $shopSample = array_slice($shopIds, 0, min(50, count($shopIds)));
        shuffle($shopSample);

        $reviewInserted = 0;
        $used = []; // track (user_id, shop_id) pairs

        foreach ($shopSample as $shopId) {
            foreach ($userIds as $uid) {
                $key = $uid . '-' . $shopId;
                if (isset($used[$key])) continue;
                $used[$key] = true;

                DB::table('coffee_shop_reviews')->insertOrIgnore([
                    'user_id'       => $uid,
                    'coffee_shop_id'=> $shopId,
                    'rating'        => rand(3, 5),
                    'review'        => $reviewTexts[array_rand($reviewTexts)],
                    'created_at'    => Carbon::now()->subDays(rand(1, 90)),
                    'updated_at'    => Carbon::now(),
                ]);
                $reviewInserted++;
            }
        }
        $this->command->info("✓ {$reviewInserted} coffee_shop_reviews ditambahkan.");

        $this->command->info('');
        $this->command->info('🎉 Semua tabel berhasil diisi!');
    }
}
