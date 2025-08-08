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
        Schema::create('kategori_wisata', function (Blueprint $table) {
            $table->id('id_kategori_wisata');
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')
                ->onDelete('cascade')
                ->onUpdate('cascade');
            $table->foreignId('id_wisata')->constrained('wisata', 'id_wisata')
                ->onDelete('cascade')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kategori_wisata');
    }
};
