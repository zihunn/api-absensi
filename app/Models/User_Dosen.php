<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User_Dosen extends Model
{
    // use HasFactory;
    use HasApiTokens, HasFactory, Notifiable;


    protected $table = 'user_dosen';

    protected $fillable = [
        'dosen_id',
        'nama',
        'password',
        'nidn',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'email',
        'gelar_depan',
        'gelar_belakang',
        'image',
    ];

    protected $hidden = [
        'password',
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
