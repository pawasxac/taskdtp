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
            $table->time('jam_buka')->change();
            $table->time('jam_tutup')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('coffee_shops', function (Blueprint $table) {
            $table->string('jam_buka')->change();
            $table->string('jam_tutup')->change();
        });
    }
};
