<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profesores;
use App\Http\Controllers\usuariosController;
use App\Models\usuarios;
use App\Services\Files;

class ProfesorController extends Controller
{
    public function index(Request $request)
    {  
        if(empty($request->idMateria)){
            return usuarios::where('ou', 'Profesor')->orderBy('created_at','desc')->get();
        }

        return usuarios::where('ou', 'Profesor')
               ->join('profesor_dicta_materia', 'profesor_dicta_materia.idProfesor', '=', 'usuarios.id')
               ->where('profesor_dicta_materia.idMateria','=',$request->idMateria)
               ->get();
    }

    public function update(Request $request, $id)
    {
        $profesor = profesores::find($id);
        $profesor->materia()->sync($request->materias);
        return usuariosController::update($request, $id);
        
    }

    public function show($id)
    {
        $profesor = profesores::find($id)->load('materia','usuario');
        $filesService = new Files();
        $profesor->usuario['imagen_perfil'] = $filesService->getImage($profesor->usuario['imagen_perfil']);
        return $profesor;
    }
}
