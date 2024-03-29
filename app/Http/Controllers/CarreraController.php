<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Carrera;
use App\Models\Grado;

class CarreraController extends Controller
{
    public function index(Request $request)
    {
        if($request->eliminado)
        {
            return response()->json(Carrera::onlyTrashed()->get());
        }
        return response()->json(Carrera::all()->load('grado'));
    }

    public function activar($id){
        $carrera = Carrera::onlyTrashed()->find($id);
        $carrera->restore();
        Grado::onlyTrashed()->where('carrera_id', $id)->restore();
        return response()->json($carrera);
    }

    public function show($id)
    {
        return response()->json(Carrera::find($id)->load('grado')->load('grado.materias'));
    }

    public function create(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|unique:carreras,nombre',
            'plan' => 'required|string|max:4',
            'categoria' => 'required|string|max:30',
            'grados' => 'array',
        ]);
        $carrera = Carrera::create($request->all());

        if ($request->grados) {
            self::agregarCarreraGrados($request->grados, $carrera);
        }

        RegistrosController::store("CARRERA", $request->header('token'), "CREATE", $request->nombre);
        return response()->json($carrera->load('grado'), 201);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'string|required',
            'plan' => 'string|max:4',
            'categoria' => 'string|max:30',
            'grados' => 'array',
        ]);

        $carrera = Carrera::findOrFail($id);
        if ($request->grados) {
            self::agregarCarreraGrados($request->grados, $carrera);
        }
        $nombreAntiguo = $carrera->nombre;
        $carrera->update($request->all());


        RegistrosController::store("CARRERA", $request->header('token'), "UPDATE", $nombreAntiguo . " - " . $carrera->nombre);
        return $carrera;
    }

    public function destroy(Request $request, $id)
    {
        if(empty($id)){
            return response()->json(['status' => "Bad request"], 404);
        }
       try { 
            $carrera = Carrera::findOrFail($id);
            foreach($carrera->grado as $grado){
                $grado->grupos()->delete();
                $grado->materias()->detach();
                $grado->delete();
            }
            $carrera->delete();

            return $carrera;
            RegistrosController::store("CARRERA", $request->header('token'), "DELETE", $carrera->nombre);
       } catch (Exception $e) {
            return response()->json(['status' => "Error al eliminar carrera"], 400);
        } 


    }

    public function destroyGrado($id, $idGrado, Request $request)
    {
        $carrera = Carrera::findOrFail($id);
        $grado = $carrera->grado->find($idGrado);

        if (empty($grado) || $grado == null) {
            return response()->json(['status' => "Grado no encontrado"], 404);
        }
        try {
            $grado->delete();
        } catch (Exception $e) {
            return response()->json(['status' => "Error al eliminar grado"], 403);
        }
        RegistrosController::store("CARRERA GRADO", $request->header('token'), "DELETE", $carrera->nombre . " " . $grado->grado);
        return response()->json(['status' => 'success']);
    }

    public function agregarCarreraGrados($grados, $carrera)
    {
        foreach ($grados as $grado) {
            if ($carrera->grado()->where('grado', $grado)->first()) {
                continue;
            }
            $g=Grado::onlyTrashed()->where('carrera_id', $carrera->id)->where('grado', $grado)->first();
            if ($g){
                $g->restore();
                continue;
            }
            $carrera->grado()->create([
                'grado' => $grado
            ]);
        }
    }

}