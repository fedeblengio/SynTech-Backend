<?php

namespace App\Http\Controllers;
use App\Models\Grado;
use App\Http\Controllers\RegistrosController;
use Illuminate\Http\Request;

class GradoController extends Controller
{
    public function update (Request $request, $id){
        $request->validate([
            'grado'=> 'required|string',
            'materias'=> 'array',
        ]);
        $grado=Grado::findOrFail($id);
        $grado->update($request->all());
        if($request->materias){
            $grado->materias()->sync($request->materias);
        }


        RegistrosController::store("GRADO", $request->header('token'), "UPDATE", $grado->carrera->nombre . " Grado: " . $grado->grado);

        return response()->json($grado->load('materias'));

    }

    public function show($id){
        return response()->json(Grado::findOrFail($id)->load('materias','grupos'));
    }

}
