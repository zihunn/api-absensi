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
        Schema::create('jadwal_dosen', function (Blueprint $table) {
            $table->id();
            $table->string('dosen_id');
            $table->string('nama_dosen');
            $table->string('matkul');
            $table->string('hari');
            $table->string('tahun_id');
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->string('tanggal_mulai');
            $table->string('tanggal_selesai');
            $table->integer('rencana_kehadiran');
            $table->integer('jumlah_mhsw');
            $table->integer('jadwal_id');
            $table->string('program_id');
            $table->string('prodi_id');
            $table->string('ruang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal_dosen');
    }
};
