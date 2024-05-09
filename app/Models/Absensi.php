<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    use HasFactory;
    protected $table = 'presensimhsw';

    protected $fillable = [
        'JadwalID',
        'KRSID',
        'PresensiID',
        'MhswID',
        'JenisPresensiID',
        'Nilai',
        'NA',
    ];
    protected $primaryKey = 'PresensiMhswID ';
}
