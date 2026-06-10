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
            if (preg_match('/\.php$|\.js$|\.jsx$|\.blade\.php$/', $path)) {
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

$removedCount = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    // Remove comments starting with // but not part of URL schemas
    $newContent = preg_replace('/(?<!http:|https:|ftp:|file:)\/\/.*$/m', '', $content);
    
    // Clean up trailing spaces from lines that had comments removed
    $lines = explode("\n", $newContent);
    $cleanedLines = [];
    foreach ($lines as $line) {
        // If line is empty or just whitespace after removing comment, let's keep it but trim it
        $trimmed = rtrim($line);
        $cleanedLines[] = $trimmed;
    }
    $finalContent = implode("\n", $cleanedLines);

    if ($finalContent !== $content) {
        file_put_contents($file, $finalContent);
        echo "Cleaned comments in: " . basename($file) . "\n";
        $removedCount++;
    }
}

echo "Total files cleaned: $removedCount\n";
