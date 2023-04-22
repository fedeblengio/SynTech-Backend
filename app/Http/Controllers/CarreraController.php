<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Models\Carrera;
use App\Models\Grado;
use App\Models\carrera_tiene_materias;
use App\Models\grupos_pertenecen_carrera;
use GrahamCampbell\ResultType\Success;
use Illuminate\Support\Facades\DB;
use LdapRecord\Query\Events\Read;

class CarreraController extends Controller
{
    public function index()
    {
        return response()->json(Carrera::all()->load('grado'));
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
        try {
            $carrera = Carrera::findOrFail($id);
            $carrera->grado()->delete();
            $carrera->delete();
            return $carrera;
            RegistrosController::store("CARRERA", $request->header('token'), "DELETE", $carrera->nombre);
        } catch (Exception $e) {
            return response()->json(['status' => "Error al eliminar carrera"], 409);
        }


    }

    public function destroyGrado($id, $idGrado, Request $request)
    {
        $carrera = Carrera::findOrFail($id);
        $grado = $carrera->grado->find($idGrado);

        if (empty($grado)) {
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
            $carrera->grado()->create([
                'grado' => $grado
            ]);
        }
    }

    public function agregarGradoMaterias(Request $request)
    {
        foreach ($request->materias as $materia) {
            carrera_tiene_materias::create($request->carrera_id, $materia->id, $materia->cantidad_horas, $request->grado_i);
        }
    }

    public function agregarCarreraGrupos(Request $request)
    {
        foreach ($request->grupo_id as $grupo)
            grupos_pertenecen_carrera::create($request->carrera_id, $request->grado_id, $grupo);
    }

}