<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class todo extends Model
{
    use HasFactory;
    protected $table = 'todo';
    protected $fillable = [
        'npm',
        'title_task',
        'desc_task',
        'status',
        'category',
        'date',
        'time',
    ];

    protected $guarded = ['id'];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
