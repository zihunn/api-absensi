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
        Schema::create('presensi_mobile', function (Blueprint $table) {
            $table->id();
            $table->string('nama_mhsw');
            $table->string('mhsw_id');
            $table->string('matkul');
            $table->date('tanggal');
            $table->string('jam_mulai');
            $table->string('jam_selesai');
            $table->string('pertemuan');
            $table->string('status');
            $table->integer('nilai');
            $table->string('dosen');
            $table->string('dosen_id');
            $table->string('jadwal_id');
            $table->string('tahun_id');
            $table->string('prodi_id');
            $table->string('semester');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_mobile');
    }
};
