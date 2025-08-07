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
        Schema::create('wisata', function (Blueprint $table) {
            $table->id('id_wisata');
            $table->string('nama_wisata', 100);
            $table->foreignId('id_kategori')->constrained('kategori', 'id_kategori')->onDelete('cascade');
            $table->text('deskripsi');
            $table->string('foto');
            $table->string('jam_operasional', 20);
            $table->decimal('harga_tiket', 10, 2);
            $table->decimal('biaya_parkir', 10, 2);
            $table->text('fasilitas');
            $table->text('peraturan');
            $table->decimal('longitude', 10, 8);
            $table->decimal('latitude', 10, 8);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wisata');
    }
};
