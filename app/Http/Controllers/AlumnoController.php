<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;
use App\Models\alumnos;
use App\Models\grupos;
use App\Models\usuarios;

class AlumnoController extends Controller
{

    public function index(Request $request)
    {   
        return usuarios::all()->where('ou', 'Alumno');
    }

    public function show($id){
        return alumnos::find($id)->load('grupos');
    }

    public function update(Request $request, $id)
    {
        $alumno = alumnos::find($id);
        $alumno->grupos()->sync($request->grupos);
        return usuariosController::update($request, $id);
    }

    public function gruposNoPertenecenAlumno($id){
        $alumno = alumnos::find($id);
        $resultado = grupos::whereDoesntHave('alumnos', function($query) use ($alumno){
            $query->where('idAlumnos', $alumno->id);
        })->get();

        return response()->json($resultado);
    }
}
