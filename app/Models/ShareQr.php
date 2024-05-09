<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShareQr extends Model
{
    use HasFactory;
    protected $table = 'share_qr';

    protected $fillable = [
        'jadwal_id',
        'presensi_id',
        'dosen_id',
        'nama_dosen',
        'file',
        'description',
        'prodi',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
