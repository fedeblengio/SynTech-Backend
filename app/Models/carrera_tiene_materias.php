<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class carrera_tiene_materias extends Model
{
    protected $fillable=[
        'carrera_id',
        'materia_id',
        'grado_id'
    ];
    use HasFactory;
}
