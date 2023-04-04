<?php

namespace App\Http\Controllers;

use App\Models\bedelias;
use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;
use App\Models\usuarios;
use Illuminate\Support\Facades\DB;
use App\Services\Files;

class BedeliaController extends Controller
{
    public function index(Request $request)
    {
        if($request->eliminados == 'true'){
            $bedeliasEliminados = DB::table('usuarios')
            ->select('*')
            ->where('deleted_at', '!=', null)
            ->where('ou', 'Bedelias')
            ->get();
            return response()->json($bedeliasEliminados);
        }
        $resultado=DB::table('usuarios')
        ->select('usuarios.id', 'usuarios.nombre', 'usuarios.email', 'usuarios.ou', 'usuarios.genero', 'bedelias.cargo')
        ->join('bedelias', 'usuarios.id', '=', 'bedelias.id')
        ->whereNull('usuarios.deleted_at')
        ->get();
        return response()->json($resultado);
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
