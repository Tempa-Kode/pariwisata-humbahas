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
        Schema::table('wisata', function (Blueprint $table) {
            $table->string('harga_tiket', 80)->nullable()->change();
            $table->string('biaya_parkir', 80)->nullable()->change();
            $table->string('transportasi', 50)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wisata', function (Blueprint $table) {
            $table->string('harga_tiket', 100)->change();
            $table->string('biaya_parkir', 100)->change();
            $table->string('transportasi', 100)->change();
        });
    }
};
