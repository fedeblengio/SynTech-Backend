<?php

namespace App\Http\Controllers;
use App\Models\Grado;
use App\Models\materia;
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

    public function agregarMateriaGrado($id,Request $request){
        $request->validate([
            'materia_id'=> 'required',
            'cantidad_horas'=> 'nullable',
        ]);
        $grado=Grado::findOrFail($id);
        $grado->materias()->attach($request->materia_id,['cantidad_horas'=> $request->cantidad_horas, 'carrera_id' => $grado->carrera_id]);
        return response()->json($grado->load('materias','grupos'));
    }
    public function eliminarMateriaGrado($idGrado,$idMateria){
        $grado=Grado::findOrFail($idGrado);
        
        $grado->materias()->detach($idMateria);
        return response()->json($grado->load('materias','grupos'));
    }

    public function show($id){
        return response()->json(Grado::findOrFail($id)->load('materias','grupos'));
    }

}
