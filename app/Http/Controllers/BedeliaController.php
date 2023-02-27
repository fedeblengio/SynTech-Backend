<?php

namespace App\Http\Controllers;

use App\Models\bedelias;
use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;

class BedeliaController extends Controller
{
    public function index(Request $request)
    {
        return bedelias::all();
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
