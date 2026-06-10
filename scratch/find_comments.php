<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

function scan_dir($dir, &$results = []) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            if (preg_match('/\.php$|\.js$|\.jsx$/', $path)) {
                $results[] = $path;
            }
        } else if ($value != "." && $value != "..") {
            scan_dir($path, $results);
        }
    }
    return $results;
}

$files = [];
scan_dir(base_path('app'), $files);
scan_dir(base_path('routes'), $files);
scan_dir(base_path('resources'), $files);

foreach ($files as $file) {
    $content = file_get_contents($file);
    $lines = explode("\n", $content);
    foreach ($lines as $i => $line) {
        if (preg_match('/(?<!https:|http:)\/\//', $line)) {
            echo basename($file) . ":" . ($i+1) . " -> " . trim($line) . "\n";
        }
    }
}
