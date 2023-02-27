<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class materia extends Model
{
    use HasFactory;
    protected $table = 'materias';
    use SoftDeletes;

    protected $fillable=[
        'nombre',
    ];

    public function grado(){
        return $this->belongsToMany(Grado::class, 'carrera_tiene_materias', 'materia_id', 'grado_id')->withTimestamps();
    }

    public function profesores(){
        return $this->belongsToMany(profesores::class, 'profesor_dicta_materia', 'idMateria', 'idProfesor')->withTimestamps();
    }
}
