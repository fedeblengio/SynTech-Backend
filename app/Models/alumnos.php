<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class alumnos extends Model
{
    protected $fillable=[
        "cedula_alumno"
    ];
    use HasFactory;
    use SoftDeletes;
}
