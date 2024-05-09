<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jadwal_Mhsw extends Model
{
    use HasFactory;
    protected $table = 'jadwal_mhsw';
    protected $fillable = [
        'npm',
        'nama_mhsw',
        'matkul',
        'hari',
        'tahun_id',
        'jam_mulai',
        'jam_selesai',
        'tanggal_mulai',
        'tanggal_selesai',
        'dosen',
        'dosenID',
    ];
}
