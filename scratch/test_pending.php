<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

foreach (['conversations', 'conversation_members', 'conversation_messages', 'notifications'] as $tbl) {
    try {
        $count = DB::table($tbl)->count();
        echo "Table $tbl count: $count\n";
    } catch (\Exception $e) {
        echo "Table $tbl error: " . $e->getMessage() . "\n";
    }
}
