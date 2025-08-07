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
        Schema::create('rute', function (Blueprint $table) {
            $table->id('id_rute');
            $table->foreignId('lokasi_asal')
                ->constrained('wisata', 'id_wisata')
                ->onDelete('cascade');
            $table->foreignId('lokasi_tujuan')
                ->constrained('wisata', 'id_wisata')
                ->onDelete('cascade');
            $table->double('jarak', 8, 2)->nullable();
            $table->double('waktu_tempuh', 8, 2)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rute');
    }
};
