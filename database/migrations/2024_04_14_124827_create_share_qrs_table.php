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
        Schema::create('share_qr', function (Blueprint $table) {
            $table->id();
            $table->integer('jadwal_id');
            $table->integer('presensi_id');
            $table->integer('dosen_id');
            $table->string('nama_dosen');
            $table->string('file');
            $table->string('prodi');
            $table->string('description');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('share_qr');
    }
};
