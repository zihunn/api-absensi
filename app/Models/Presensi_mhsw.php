<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Presensi_mhsw extends Model
{
    use HasFactory;
    protected $table = 'presensi_mobile';

    protected $fillable = [
        'nama_mhsw',
        'mhsw_id',
        'matkul',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'pertemuan',
        'status',
        'nilai',
        'dosen',
        'dosen_id',
        'jadwal_id',
        'tahun_id',
        'prodi_id',
        'semester'
    ];
}
