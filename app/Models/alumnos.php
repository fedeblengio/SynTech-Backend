<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class alumnos extends Model
{
    use HasFactory;
    use SoftDeletes;

    public function grupos()
    {
        return $this->belongsToMany(grupos::class, 'alumnos_pertenecen_grupos', 'Cedula_Alumno', 'idGrupo')->withTimestamps();
        
    }

    public function asignarGrupo($grupos)
    {
        $this->grupos()->sync($grupos);
    }

    public function usuario()
    {
        return $this->belongsTo(usuarios::class, 'usuarios', 'Cedula_Alumno', 'id');
    }

}
