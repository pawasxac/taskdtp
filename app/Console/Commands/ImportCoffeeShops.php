<?php

namespace App\Console\Commands;

use App\Models\CoffeeShop;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ImportCoffeeShops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:coffee-shops {file=database/data/coffee_shops.csv}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import coffee shops from CSV file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $file = $this->argument('file');
        
        if (!file_exists($file)) {
            $this->error("File not found: {$file}");
            return 1;
        }

        try {
            // Clear existing coffee shops
            $this->info('Clearing existing coffee shops...');
            \DB::statement('SET FOREIGN_KEY_CHECKS=0');
            CoffeeShop::truncate();
            \DB::statement('SET FOREIGN_KEY_CHECKS=1');

            // Open and read the CSV file
            $this->info('Importing coffee shops from CSV...');
            $file_handle = fopen($file, 'r');
            
            // Skip header row
            fgetcsv($file_handle);
            
            $count = 0;
            $batch = [];
            
            while (($row = fgetcsv($file_handle)) !== false) {
                $batch[] = [
                    'osm_id' => $row[0],
                    'nama' => $row[1],
                    'daerah' => $row[2],
                    'kecamatan' => $row[3],
                    'alamat' => $row[4],
                    'jam_buka' => $row[5],
                    'jam_tutup' => $row[6],
                    'harga_min' => (int)$row[7],
                    'harga_max' => (int)$row[8],
                    'rating' => (float)$row[9],
                    'deskripsi' => $row[10],
                    'kecamatan_id' => (int)$row[11],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
                
                // Insert in batches of 50
                if (count($batch) >= 50) {
                    CoffeeShop::insert($batch);
                    $count += count($batch);
                    $batch = [];
                }
            }
            
            // Insert remaining records
            if (!empty($batch)) {
                CoffeeShop::insert($batch);
                $count += count($batch);
            }
            
            fclose($file_handle);
            
            $this->info("Successfully imported {$count} coffee shops!");
            return 0;
            
        } catch (\Exception $e) {
            $this->error('Error importing coffee shops: ' . $e->getMessage());
            return 1;
        }
    }
}
