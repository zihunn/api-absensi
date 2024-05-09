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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('npm')->nullable();
            $table->string('role');
            $table->string('dosen_id')->nullable();
            $table->string('email')->nullable();
            $table->string('device_id')->unique()->nullable();
            $table->string('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('image')->nullable();
            $table->string('password');
            $table->integer('sks')->nullable();
            $table->integer('hadir')->nullable();
            $table->integer('izin')->nullable();
            $table->integer('sakit')->nullable();
            $table->integer('alpa')->nullable();
            $table->string('jenis_kelamin')->nullable();
            $table->string('prodi_id')->nullable();
            $table->string('prodi_en')->nullable();
            $table->string('prodiID')->nullable();
            $table->string('programID')->nullable();
            $table->string('tahun_id')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('nidn')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            $table->string('status_krs')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }
    //gtx1050

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
