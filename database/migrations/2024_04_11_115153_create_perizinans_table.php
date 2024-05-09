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
        Schema::create('perizinan_presensi', function (Blueprint $table) {
            $table->id();
            $table->string('npm');
            $table->string('prodi');
            $table->string('angkatan');
            $table->integer('presensi_id');
            $table->integer('jadwal_id');
            $table->integer('krs_id');
            $table->string('description');
            $table->string('category');
            $table->string('file')->nullable();
            $table->string('dosen_primary');
            $table->string('dosen_secondary')->nullable();
            $table->boolean('read_primary')->default('0');
            $table->boolean('read_secondary')->default('0');
            $table->string('approve_by')->nullable();
            $table->date('created_at');
            // $table->timestamps();
            // $table->dropColumn('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('perizinan_presensi');
    }
};
