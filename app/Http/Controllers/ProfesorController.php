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
       return usuarios::where('ou', 'Profesor')->orderBy('created_at','desc')->get();
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
