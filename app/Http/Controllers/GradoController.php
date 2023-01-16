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
            'grupos'=> 'array',
        ]);
        $grado=Grado::findOrFail($id);
        $grado->update($request->all());
        if($request->materias){
            $grado->materias()->sync($request->materias);
        }
        if($request->grupos){
            $this->agregarGruposGrado($request->grupos, $grado);
        }

        RegistrosController::store("GRADO", $request->header('token'), "UPDATE", $grado->carrera->nombre . " Grado: " . $grado->grado);

        return response()->json($grado->load('materias'));

    }

    public function agregarGruposGrado($grupos,$grado){
        foreach ($grupos as $grupo) {
            if($grado->grupos()->where('idGrupo', $grupo->idGrupo)->first()){
                continue;
            }
            $grado->grupos()->create($grupo);
        }
    }
}
