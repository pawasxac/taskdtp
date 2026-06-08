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
        Schema::table('coffee_shops', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('deskripsi');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->string('photo_url')->nullable()->after('longitude');
            $table->boolean('is_verified')->default(false)->after('photo_url');
            $table->boolean('is_active')->default(true)->after('is_verified');
            $table->softDeletes()->after('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coffee_shops', function (Blueprint $table) {
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('photo_url');
            $table->dropColumn('is_verified');
            $table->dropColumn('is_active');
            $table->dropSoftDeletes();
        });
    }
};
