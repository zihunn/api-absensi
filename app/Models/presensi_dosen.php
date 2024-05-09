<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class presensi_dosen extends Model
{
    use HasFactory;

    protected $table = 'presensi';

    protected $fillable = [
        'HonorDosenID',
        'TahunID',
        'JadwalID',
        'Pertemuan',
        'DosenID',
        'Tanggal',
        'JamMulai',
        'JamSelesai',
        'Hitung',
        'Catatan',
        'SKSHonor',
        'TunjanganSKS',
        'TunjanganTransport',
        'TunjanganTetap',
        'NA',
        'LoginBuat',
        'TanggalBuat',
        'LoginEdit',
        'TanggalEdit',
    ];
    protected $primaryKey = 'PresensiID';
}
