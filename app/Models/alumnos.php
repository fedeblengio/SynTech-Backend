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
        return $this->belongsToMany(grupos::class, 'alumnos_pertenecen_grupos', 'idAlumnos', 'idGrupo')->withTimestamps();
        
    }

    public function asignarGrupos($grupos,$idAlumno)
    {
        $agregarGrupo = new alumnos_pertenecen_grupos();
        foreach($grupos as $grupo){
            $agregarGrupo->idAlumnos= $idAlumno;
            $agregarGrupo->idGrupo = $grupo;
            $agregarGrupo->save();
        }
    }

    public function usuario(){
        return $this->belongsTo(usuarios::class,'id', 'id');
    }

}
