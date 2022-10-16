<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\materia;
use Carbon\Carbon;
use App\Http\Controllers\RegistrosController;

class agregarMateriaController extends Controller
{
    public function index()
    {
        return response()->json(materia::all());
    }

    public function store(Request $request)
    {

        $existeMateria = DB::table('materias')
            ->select('*')
            ->where('nombre', $request->nombreMateria)
            ->first();

        if ($existeMateria) {
            if ($existeMateria->deleted_at) {

                DB::table('materias')
                    ->where('nombre', $request->nombreMateria)
                    ->update(['deleted_at' => null]);

                RegistrosController::store("MATERIA", $request->header('token'), "ACTIVATE", $request->nombreMateria);

                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Materia Existe'], 416);
        } else {
            return $this->agregarMateria($request);
        }
    }

    /*     public function activarProfesorMateria($existeMateria)
    {
        DB::table('profesor_dicta_materia')
            ->where('idMateria', $existeMateria->idMateria)
            ->update(['deleted_at' => null]);
    }
    public function activarGrupoMateria($existeMateria)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idMateria', $existeMateria->idMateria)
            ->update(['deleted_at' => null]);
    } */
    public function show(Request $request)
    {
        return response()->json(materia::where('id', $request->idMateria)->get());
    }


    public function update(Request $request)
    {
        try {
            $modificarMateria = materia::where('id', $request->idMateria)->first();
            $nombreViejo = $modificarMateria->nombre;
            $modificarMateria->nombre = $request->nuevoNombre;
            $modificarMateria->save();
            RegistrosController::store("MATERIA", $request->header('token'), "UPDATE", $nombreViejo . " - " . $request->nuevoNombre);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function destroy(Request $request)
    {
        $eliminarMateria = materia::where('id', $request->idMateria)->first();
        $nombreMateria = $eliminarMateria->nombre;
        try {
            $eliminarMateria->delete();
            self::eliminarMateriaProfesor($request, $nombreMateria);
            self::eliminarMateriaGrupo($request, $nombreMateria);
            RegistrosController::store("MATERIA", $request->header('token'), "DELETE", $nombreMateria);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function eliminarMateriaProfesor($request, $materia)
    {
        DB::table('profesor_dicta_materia')
            ->where('idMateria', $request->idMateria)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("MATERIA PROFESOR", $request->header('token'), "DELETE", $materia);
    }
    public function eliminarMateriaGrupo($request, $materia)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idMateria', $request->idMateria)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("MATERIA GRUPO", $request->header('token'), "DELETE", $materia);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agregarMateria(Request $request): \Illuminate\Http\JsonResponse
    {
        $agregarMateria = new materia;
        $agregarMateria->nombre = $request->nombreMateria;
        $agregarMateria->save();
        RegistrosController::store("MATERIA", $request->header('token'), "CREATE", $request->nombreMateria);
        return response()->json(['status' => 'Success'], 200);
    }
}
