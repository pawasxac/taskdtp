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
            $table->foreignId('kecamatan_id')->nullable()->constrained('kecamatans')->onDelete('set null')->after('kecamatan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coffee_shops', function (Blueprint $table) {
            $table->dropForeign(['kecamatan_id']);
            $table->dropColumn('kecamatan_id');
        });
    }
};

