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
    Schema::create('coffee_shops', function (Blueprint $table) {
        $table->id();
        $table->string('nama');
        $table->string('daerah'); // Surabaya / Sidoarjo
        $table->string('kecamatan');
        $table->text('alamat');
        $table->string('jam_buka');
        $table->integer('harga_min');
        $table->integer('harga_max');
        $table->decimal('rating', 2, 1)->default(0);
        $table->text('deskripsi');
        $table->timestamps();
    });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coffee_shops');
    }
};
