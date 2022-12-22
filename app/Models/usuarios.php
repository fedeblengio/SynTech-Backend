<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
 
class usuarios extends Model
{
    protected $fillable=[
        "cedula",
        "nombre",
        "email",
        "ou",
        "genero",
        "imagen_perfil"
    ];
    use HasFactory;
    use SoftDeletes;
}
