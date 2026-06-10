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
        Schema::dropIfExists('gathering_requests');
        Schema::dropIfExists('community_comments');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented to prevent accidentally bringing them back without columns
    }
};
