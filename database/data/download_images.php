<?php

// Tentukan folder penyimpanan
$targetDir = __DIR__ . '/../../storage/app/public/coffee-shops';

// Buat folder jika belum ada
if (!file_exists($targetDir)) {
    mkdir($targetDir, 0755, true);
    echo "📁 Folder berhasil dibuat: {$targetDir}\n";
}

// Koleksi keyword pencarian agar gambar sangat relevan dengan suasana kafe
$keywords = [
    'coffee-shop', 'cafe-interior', 'aesthetic-cafe', 'indonesian-cafe',
    'espresso-bar', 'barista-station', 'cozy-coffee-shop', 'coffee-table'
];

$totalImages = 180;

echo "⏳ Memulai download {$totalImages} gambar kafe estetik...\n";

for ($i = 1; $i <= $totalImages; $i++) {
    // List 15 URL Unsplash Kafe Estetik yang Berbeda-beda dan Berfungsi Baik
    $coffeeUrls = [
        "https://images.unsplash.com/photo-1554118811-1e0d58224f24?q=80&w=600&auto=format&fit=crop", // Cozy Cafe
        "https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?q=80&w=600&auto=format&fit=crop", // Modern Cafe Interior
        "https://images.unsplash.com/photo-1498804103079-a6351b050096?q=80&w=600&auto=format&fit=crop", // Coffee pouring
        "https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?q=80&w=600&auto=format&fit=crop", // Cafe Table setup
        "https://images.unsplash.com/photo-1521017432531-fbd92d768814?q=80&w=600&auto=format&fit=crop", // Aesthetic espresso bar
        "https://images.unsplash.com/photo-1447933601403-0c6688de566e?q=80&w=600&auto=format&fit=crop", // Coffee beans and cup
        "https://images.unsplash.com/photo-1507133750040-4a8f57021571?q=80&w=600&auto=format&fit=crop", // Latte art cup
        "https://images.unsplash.com/photo-1468424907315-9a2503d42b44?q=80&w=600&auto=format&fit=crop", // Industrial style cafe
        "https://images.unsplash.com/photo-1509042239860-f550ce710b93?q=80&w=600&auto=format&fit=crop", // Pour over coffee
        "https://images.unsplash.com/photo-1511920170033-f8396924c348?q=80&w=600&auto=format&fit=crop", // Vintage cafe mood
        "https://images.unsplash.com/photo-1559925393-8be0ec4767c8?q=80&w=600&auto=format&fit=crop", // Friends at cafe
        "https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?q=80&w=600&auto=format&fit=crop", // Coffee and book
        "https://images.unsplash.com/photo-1541167760496-1628856ab772?q=80&w=600&auto=format&fit=crop", // Latte cup top down
        "https://images.unsplash.com/photo-1453614512568-c4024d13c247?q=80&w=600&auto=format&fit=crop", // Cafe storefront / glass
        "https://images.unsplash.com/photo-1517256064527-09c53b2d0bc6?q=80&w=600&auto=format&fit=crop"  // Minimalist bar
    ];

    // Ambil URL secara siklik dari daftar di atas
    $url = $coffeeUrls[($i - 1) % count($coffeeUrls)];
    
    // Download menggunakan cURL
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    
    $imageData = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && !empty($imageData)) {
        $fileName = "cafe-{$i}.jpg";
        $filePath = "{$targetDir}/{$fileName}";
        file_put_contents($filePath, $imageData);
        echo "✅ [{$i}/{$totalImages}] Downloaded: {$fileName}\n";
    } else {
        echo "❌ [{$i}/{$totalImages}] Gagal mendownload gambar.\n";
    }
    
    // Jeda singkat
    usleep(100000); // 0.1 detik jeda cepat karena URL langsung bypass CDN
}

echo "🎉 Selesai! Semua gambar berhasil disimpan di: storage/app/public/coffee-shops/\n";
