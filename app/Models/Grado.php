<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grado extends Model
{
    protected $fillable=[
        'grado',
        'carrera_id',
    ];
    use HasFactory;
}
