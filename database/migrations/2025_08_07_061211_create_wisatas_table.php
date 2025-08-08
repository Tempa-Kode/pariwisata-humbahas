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
            $table->string('lokasi', 150);
            $table->text('deskripsi');
            $table->string('foto');
            $table->string('jam_operasional', 20)->nullable();
            $table->string('harga_tiket', 100)->nullable();
            $table->string('biaya_parkir', 100)->nullable();
            $table->text('fasilitas')->nullable();
            $table->text('peraturan')->nullable();
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
