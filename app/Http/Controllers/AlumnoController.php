<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;
use App\Models\alumnos;
use App\Models\grupos;
use App\Models\usuarios;
use App\Services\Files;

class AlumnoController extends Controller
{

    public function index(Request $request)
    {   
        return usuarios::where('ou', 'Alumno')->orderBy('created_at','desc')->get();
    }

    public function show($id){

        $alumno = alumnos::find($id)->load('grupos','usuario');
        $filesService = new Files();
        $alumno->usuario['imagen_perfil'] = $filesService->getImage($alumno->usuario['imagen_perfil']);
        return $alumno;
       
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
