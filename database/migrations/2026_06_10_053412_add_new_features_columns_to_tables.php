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
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_seen_at')->nullable()->after('updated_at');
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->boolean('deleted_for_sender')->default(false)->after('read_at');
            $table->boolean('deleted_for_receiver')->default(false)->after('deleted_for_sender');
            $table->boolean('deleted_for_everyone')->default(false)->after('deleted_for_receiver');
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->boolean('deleted_for_everyone')->default(false)->after('content');
            $table->json('deleted_by_users')->nullable()->after('deleted_for_everyone');
            $table->json('read_by_users')->nullable()->after('deleted_by_users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('last_seen_at');
        });

        Schema::table('direct_messages', function (Blueprint $table) {
            $table->dropColumn(['deleted_for_sender', 'deleted_for_receiver', 'deleted_for_everyone']);
        });

        Schema::table('community_posts', function (Blueprint $table) {
            $table->dropColumn(['deleted_for_everyone', 'deleted_by_users', 'read_by_users']);
        });
    }
};
