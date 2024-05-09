<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perizinan extends Model
{
    use HasFactory;
    protected $table = 'perizinan_presensi';
    public $timestamps = false;

    protected $fillable = [
        'npm',
        'prodi',
        'angkatan',
        'presensi_id',
        'jadwal_id',
        'krs_id',
        'description',
        'category',
        'file',
        'dosen_primary',
        'dosen_secondary',
        'read_primary',
        'read_secondary',
        'approve_by',
        'created_at',
    ];

    // protected $hidden = [
    //     'updated_at',
    // ];
}
