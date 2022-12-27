<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class grupos_pertenecen_carrera extends Model
{
    protected $fillable=[
        'carrera_id',
        'grado_id',
        'grupo_id'
    ];
    use HasFactory;
}
