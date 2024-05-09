<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PresensiMhsw extends Model
{
    use HasFactory;
    protected $table = 'presensimhsw';
    public $timestamps = false;

    protected $fillable = [
        'JadwalID',
        'KRSID',
        'PresensiID',
        'MhswID',
        'JenisPresensiID',
        'Nilai',
        'NA'
    ];
}
