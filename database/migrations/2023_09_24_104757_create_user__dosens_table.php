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
        Schema::create('user_dosen', function (Blueprint $table) {
            $table->id();
            $table->string('dosen_id')->unique();
            // $table->string('nama');
            // $table->string('password');
            $table->string('nidn');
            $table->string('tempat_lahir');
            // $table->string('tanggal_lahir');
            // $table->string('no_hp');
            // $table->string('email');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang')->nullable();
            // $table->string('image')->nullable();
            // $table->rememberToken();
            // $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_dosen');
    }
};
