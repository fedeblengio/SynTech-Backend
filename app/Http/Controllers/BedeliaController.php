<?php

namespace App\Http\Controllers;

use App\Models\bedelias;
use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;
use App\Models\usuarios;

class BedeliaController extends Controller
{
    public function index(Request $request)
    {
        return usuarios::where('ou', 'Bedelias')->orderBy('created_at','desc')->get();
    }

    public function show($id){
        return bedelias::find($id);
    }

    public function update(Request $request, $id)
    {
        $bedelia = bedelias::find($id);
        $bedelia->update($request->all());
        return usuariosController::update($request, $id);
    }

}
