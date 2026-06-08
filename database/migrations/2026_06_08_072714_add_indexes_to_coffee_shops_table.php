<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('coffee_shops', function (Blueprint $table) {
            $table->index('nama');
            $table->index('rating');
            $table->index('harga_min');
            $table->index('harga_max');
            $table->index(['is_verified', 'is_active']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coffee_shops', function (Blueprint $table) {
            $table->dropIndex(['nama']);
            $table->dropIndex(['rating']);
            $table->dropIndex(['harga_min']);
            $table->dropIndex(['harga_max']);
            $table->dropIndex(['is_verified', 'is_active']);
        });
    }
};
