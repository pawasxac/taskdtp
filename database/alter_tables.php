<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

Schema::table('global_chats', function (Blueprint $table) {
    if (!Schema::hasColumn('global_chats', 'reply_to_id')) {
        $table->foreignId('reply_to_id')->nullable()->constrained('global_chats')->nullOnDelete();
    }
});

Schema::table('community_posts', function (Blueprint $table) {
    if (!Schema::hasColumn('community_posts', 'reply_to_id')) {
        $table->foreignId('reply_to_id')->nullable()->constrained('community_posts')->nullOnDelete();
    }
});

echo "Success\n";
