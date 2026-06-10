<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('global_chats', function (Blueprint $table) {
            $table->foreignId('reply_to_id')->nullable()->constrained('global_chats')->nullOnDelete();
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->foreignId('reply_to_id')->nullable()->constrained('community_posts')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('global_chats', function (Blueprint $table) {
            $table->dropForeign(['reply_to_id']);
            $table->dropColumn('reply_to_id');
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropForeign(['reply_to_id']);
            $table->dropColumn('reply_to_id');
        });
    }
};
