<?php

function scan_dir($dir, &$results = []) {
    $files = scandir($dir);
    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            // Only target .php files, NOT .blade.php
            if (preg_match('/\.php$/', $path) && !str_contains($path, '.blade.php')) {
                $results[] = $path;
            }
        } else if ($value != "." && $value != "..") {
            scan_dir($path, $results);
        }
    }
    return $results;
}

$files = [];
scan_dir(__DIR__ . '/../app', $files);
scan_dir(__DIR__ . '/../routes', $files);

$removedCount = 0;
foreach ($files as $file) {
    $content = file_get_contents($file);
    $tokens = token_get_all($content);
    $newContent = '';

    foreach ($tokens as $token) {
        if (is_array($token)) {
            list($id, $text) = $token;
            if ($id === T_COMMENT) {
                // Check if it's a double slash comment
                if (str_starts_with(ltrim($text), '//')) {
                    // Preserve newlines at the end of the comment
                    $newlines = substr($text, strcspn($text, "\r\n"));
                    $newContent .= $newlines;
                } else {
                    $newContent .= $text;
                }
            } else {
                $newContent .= $text;
            }
        } else {
            $newContent .= $token;
        }
    }

    // Clean up trailing spaces from lines
    $lines = explode("\n", $newContent);
    $cleanedLines = array_map('rtrim', $lines);
    $finalContent = implode("\n", $cleanedLines);

    if ($finalContent !== $content) {
        file_put_contents($file, $finalContent);
        echo "Cleaned PHP comments in: " . basename($file) . "\n";
        $removedCount++;
    }
}

echo "Total PHP files cleaned: $removedCount\n";
