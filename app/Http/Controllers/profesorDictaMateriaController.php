<?php

namespace App\Http\Controllers;

use App\Models\materia;
use App\Models\profesores;

class profesorDictaMateriaController extends Controller
{
    public function materiasNoPertenecenProfesor($id)
    {

    $profesor = profesores::find($id);
    $resultado = materia::whereDoesntHave('profesores', function($query) use ($profesor) 
    {$query->where('idProfesor', $profesor->id);})
    ->get();

    return response()->json($resultado); 
    }

}
