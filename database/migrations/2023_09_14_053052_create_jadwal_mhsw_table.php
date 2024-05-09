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
        Schema::create('jadwal_mhsw', function (Blueprint $table) {
            $table->id();
            $table->string('npm');
            $table->string('nama_mhsw');
            $table->string('matkul');
            $table->string('hari');
            $table->string('tahun_id');
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->string('tanggal_mulai');
            $table->string('tanggal_selesai');
            $table->string('dosen');
            $table->string('dosenID');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_mhsw');
    }
};
