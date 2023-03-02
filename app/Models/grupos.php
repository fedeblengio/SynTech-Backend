<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class grupos extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'grupos';
    protected $fillable = ['idGrupo', 'nombreCompleto','anioElectivo','id_grado'];

    public function grado()
    {
        return $this->belongsTo(Grado::class, 'id_grado');
    }

    public function alumnos()
    {
        return $this->belongsToMany(alumnos::class, 'alumnos_pertenecen_grupos', 'idGrupo', 'idAlumnos')->withTimestamps();
    }

    public function profesores()
    {
        return $this->belongsToMany(profesores::class, 'grupos_tienen_profesor', 'idGrupo', 'idProfesor')->withTimestamps();
    }
}
