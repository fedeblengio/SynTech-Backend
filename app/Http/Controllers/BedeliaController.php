<?php

namespace App\Http\Controllers;

use App\Models\bedelias;
use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;
use App\Models\usuarios;
use App\Services\Files;

class BedeliaController extends Controller
{
    public function index(Request $request)
    {
        return usuarios::where('ou', 'Bedelias')->orderBy('created_at','desc')->get();
    }
    public function show($id){
        $bedelia = bedelias::find($id)->load('usuario');
        $filesService = new Files();
        $bedelia->usuario['imagen_perfil'] = $filesService->getImage($bedelia->usuario['imagen_perfil']);
        return $bedelia;
    }

    public function update(Request $request, $id)
    {
        $bedelia = bedelias::find($id);
        $bedelia->update($request->all());
        return usuariosController::update($request, $id);
    }

}
