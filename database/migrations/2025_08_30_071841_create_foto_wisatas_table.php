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
        Schema::create('foto_wisata', function (Blueprint $table) {
            $table->id('id_foto_wisata');
            $table->foreignId('id_wisata')->constrained('wisata', 'id_wisata')->onDelete('cascade');
            $table->string('url_foto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('foto_wisata');
    }
};
