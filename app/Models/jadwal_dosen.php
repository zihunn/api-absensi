<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwal_dosen extends Model
{
    use HasFactory;
    protected $table = 'jadwal_dosen';
    protected $fillable = [
        'dosen_id',
        'nama_dosen',
        'matkul',
        'hari',
        'tahun_id',
        'jam_mulai',
        'jam_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'rencana_kehadiran',
        'jumlah_mhsw',
        'jadwal_id',
        'program_id',
        'prodi_id',
        'ruang'
    ];
}
