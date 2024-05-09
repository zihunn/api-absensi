<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class jadwaldosen extends Model
{
    use HasFactory;
    protected $table = 'jadwaldosen';
    public $timestamps = false;
    protected $fillable = [
        'JadwalID',
        'DosenID',
        'JenisDosenID',
        'TglBuat',
        'LoginBuat',
    ];
  
}
