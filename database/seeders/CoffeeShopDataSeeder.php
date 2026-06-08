<?php

namespace Database\Seeders;

use App\Models\CoffeeShop;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class CoffeeShopDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Path ke file CSV
        $csvPath = database_path('data/coffee_shops.csv');

        // Cek apakah file ada
        if (!File::exists($csvPath)) {
            $this->command->error("File CSV tidak ditemukan: {$csvPath}");
            return;
        }

        // Buka file CSV
        $file = fopen($csvPath, 'r');
        if (!$file) {
            $this->command->error("Tidak bisa membuka file CSV");
            return;
        }

        // Skip header row
        $header = fgetcsv($file);
        $count = 0;

        // Baca setiap baris CSV
        while (($row = fgetcsv($file)) !== false) {
            // Pastikan jumlah kolom sesuai
            if (count($row) < 11) {
                continue;
            }

            // Map data dari CSV ke database
            CoffeeShop::create([
                'nama' => $row[1],           // nama
                'daerah' => $row[2],         // daerah
                'kecamatan' => $row[3],      // kecamatan
                'alamat' => $row[4],         // alamat
                'jam_buka' => $row[5],       // jam_buka
                'jam_tutup' => $row[6],      // jam_tutup
                'harga_min' => (int)$row[7], // harga_min
                'harga_max' => (int)$row[8], // harga_max
                'rating' => (float)$row[9],  // rating
                'deskripsi' => $row[10],     // deskripsi
                'kecamatan_id' => (int)$row[11], // kecamatan_id
                'latitude' => isset($row[12]) ? (float)$row[12] : null,
                'longitude' => isset($row[13]) ? (float)$row[13] : null,
                'photo_url' => isset($row[14]) ? $row[14] : null,
                'is_verified' => isset($row[15]) ? (bool)$row[15] : false,
                'is_active' => isset($row[16]) ? (bool)$row[16] : true,
            ]);

            $count++;
        }

        fclose($file);
        $this->command->info("Berhasil mengimpor {$count} data coffee shop dari CSV");
    }
}
