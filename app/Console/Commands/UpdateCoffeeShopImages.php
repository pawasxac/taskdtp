<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateCoffeeShopImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-coffee-shop-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Memetakan 180 foto cafe random ke data coffee shop di database.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $shops = \App\Models\CoffeeShop::all();
        $totalImages = 180;
        
        $this->info("Ditemukan " . $shops->count() . " coffee shops.");
        
        $count = 0;
        foreach ($shops as $index => $shop) {
            // Gunakan modulo agar jika shop > 180, kembali ke foto 1
            $imageNumber = ($index % $totalImages) + 1;
            $photoPath = "/storage/coffee-shops/cafe-{$imageNumber}.jpg";
            
            $shop->photo_url = $photoPath;
            $shop->save();
            
            $count++;
        }
        
        $this->info("Berhasil memperbarui {$count} coffee shop dengan foto baru.");
    }
}
