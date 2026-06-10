<?php
// Test parsing PHP comments safely using token_get_all

$file = __DIR__ . '/../app/Http/Controllers/AuthController.php';
$content = file_get_contents($file);
$tokens = token_get_all($content);

$newContent = '';
foreach ($tokens as $token) {
    if (is_array($token)) {
        list($id, $text) = $token;
        if ($id === T_COMMENT) {
            // Check if it's a double slash comment
            if (str_starts_with(ltrim($text), '//')) {
                // If it contains newlines at the end, preserve them to avoid line shifting
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

file_put_contents(__DIR__ . '/test_auth_cleaned.php', $newContent);
echo "Done testing AuthController\n";
