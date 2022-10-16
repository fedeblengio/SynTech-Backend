<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\grupos;
use App\Models\alumnos_pertenecen_grupos;
use Carbon\Carbon;
use App\Http\Controllers\RegistrosController;

class gruposController extends Controller
{
    public function index()
    {
        return response()->json(grupos::all());
    }

    public function create(Request $request)
    {
        $gruposDB = DB::table('grupos')
            ->select('*')
            ->where('idGrupo', $request->idGrupo)
            ->first();

        if ($gruposDB) {
            if ($gruposDB->deleted_at) {
                return $this->activarGrupo($request);
            }
            return response()->json(['error' => 'Forbidden'], 416);
        } else {
            return $this->agregarGrupo($request);
        }
    }

    public function show(request $request)
    {
        return response()->json(grupos::where('idGrupo', $request->idGrupo)->first());
    }

    public function destroy(request $request)
    {
        $grupo = grupos::where('idGrupo', $request->idGrupo)->first();

        try {
            if ($grupo) {
                self::eliminarProfesoresGrupo($request);
                self::eliminarAlumnosGrupo($request);
                RegistrosController::store("GRUPO", $request->header('token'), "DELETE", $request->idGrupo);
                $grupo->delete();
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function eliminarProfesoresGrupo($request)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idGrupo', $request->idGrupo)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("GRUPO PROFESOR", $request->header('token'), "UPDATE", $request->idGrupo);
    }

    public function eliminarAlumnosGrupo($request)
    {
        DB::table('alumnos_pertenecen_grupos')
            ->where('idGrupo', $request->idGrupo)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("GRUPO ALUMNOS", $request->header('token'), "CREATE", $request->idGrupo);
    }


    public function update(request $request)
    {
        $existe = grupos::where('idGrupo', $request->idGrupo)->first();
        try {
            if ($existe) {
                if ($request->nuevoGrupo) {
                    DB::update('UPDATE grupos SET idGrupo="' . $request->nuevoGrupo . '" ,  nombreCompleto="' . $request->nuevoNombreCompleto . '" WHERE idGrupo="' . $request->idGrupo . '";');
                    RegistrosController::store("GRUPO", $request->header('token'), "UPDATE", $request->idGrupo . " - " . $request->nuevoGrupo);
                    return response()->json(['status' => 'Success'], 200);
                } else {
                    DB::update('UPDATE grupos SET idGrupo="' . $request->idGrupo . '" ,  nombreCompleto="' . $request->nuevoNombreCompleto . '" WHERE idGrupo="' . $request->idGrupo . '";');
                    RegistrosController::store("GRUPO", $request->header('token'), "UPDATE", $request->idGrupo . " - " . $request->nuevoNombreCompleto);
                    return response()->json(['status' => 'Success'], 200);
                }
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function activarGrupo(Request $request): \Illuminate\Http\JsonResponse
    {
        DB::table('grupos')
            ->where('idGrupo', $request->idGrupo)
            ->update(['deleted_at' => null]);
        RegistrosController::store("GRUPO", $request->header('token'), "ACTIVATE", $request->idGrupo);
        return response()->json(['status' => 'Success'], 200);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function agregarGrupo(Request $request): \Illuminate\Http\JsonResponse
    {
        $gruposDB = new grupos;
        $gruposDB->idGrupo = $request->idGrupo;
        $gruposDB->nombreCompleto = $request->nombreCompleto;
        $gruposDB->anioElectivo = Carbon::now()->format('Y');
        $gruposDB->save();
        RegistrosController::store("GRUPO", $request->header('token'), "CREATE", $request->idGrupo);
        return response()->json(['status' => 'Success'], 200);
    }
}
