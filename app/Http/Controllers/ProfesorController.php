<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profesores;
use App\Http\Controllers\usuariosController;
use App\Models\usuarios;

class ProfesorController extends Controller
{
    public function index(Request $request)
    {
       return usuarios::where('ou', 'Profesor')->get();
    }

    public function update(Request $request, $id)
    {
        $profesor = profesores::find($id);
        $profesor->materia()->sync($request->materias);
        return usuariosController::update($request, $id);
        
    }

    public function show($id)
    {
        return profesores::find($id)->load('materia');
    }
}
