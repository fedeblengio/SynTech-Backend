<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\usuarios;
use App\Models\grupos;
use Carbon\Carbon;
use App\Models\alumnos_pertenecen_grupos;
use App\Http\Controllers\RegistrosController;


class agregarUsuarioGrupoController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function agregarAlumnoGrupo(Request $request): \Illuminate\Http\JsonResponse
    {
        $agregarAlumnoGrupo = new alumnos_pertenecen_grupos;
        $agregarAlumnoGrupo->idGrupo = $request->idGrupo;
        $agregarAlumnoGrupo->idAlumnos = $request->idAlumno;
        $agregarAlumnoGrupo->save();
        RegistrosController::store("ALUMNO GRUPO", $request->header('token'), "CREATE", $request->idAlumno . " - " . $request->idGrupo);
        return response()->json(['status' => 'Success'], 200);

    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public static function activarAlumnoGrupo(Request $request): \Illuminate\Http\JsonResponse
    {
        DB::table('alumnos_pertenecen_grupos')
            ->where('idAlumnos', $request->idAlumno)
            ->where('idGrupo', $request->idGrupo)
            ->update(['deleted_at' => null]);
        RegistrosController::store("ALUMNO GRUPO", $request->header('token'), "ACTIVATE", $request->idAlumno . " - " . $request->idGrupo);
        return response()->json(['status' => 'Success'], 200);
    }

    public function index(Request $request)
    { 
        $resultado=DB::table('usuarios')
        ->select('usuarios.id AS id', 'usuarios.nombre', 'usuarios.email', 'alumnos_pertenecen_grupos.idGrupo')  
        ->join('alumnos', 'usuarios.id', '=', 'alumnos.id')
        ->leftJoin('alumnos_pertenecen_grupos', function($join) use ($request){
            $join->on('usuarios.id', '=', 'alumnos_pertenecen_grupos.idAlumnos')
            ->where('alumnos_pertenecen_grupos.idGrupo' , '=', $request->idGrupo);
        })
        ->whereNull('alumnos_pertenecen_grupos.idGrupo')
        
        ->whereNull('alumnos_pertenecen_grupos.deleted_at')
        ->whereNull('usuarios.deleted_at')
        ->get();

        
        return response()->json($resultado);
    }

    public static function store(Request $request)
    {
        $alumnoGrupo = DB::table('alumnos_pertenecen_grupos')
            ->select('*')
            ->where('idAlumnos', $request->idAlumno)
            ->where('idGrupo', $request->idGrupo)
            ->first();
        if ($alumnoGrupo) {
            if ($alumnoGrupo->deleted_at) {
                return self::activarAlumnoGrupo($request);
            }
        } else {
            return self::agregarAlumnoGrupo($request);
        }
    }
    public function destroy(request $request)
    {
        try {
            DB::table('alumnos_pertenecen_grupos')
                ->where('idAlumnos', $request->idAlumno)
                ->where('idGrupo', $request->idGrupo)
                ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
            RegistrosController::store("ALUMNO GRUPO", $request->header('token'), "DELETE", $request->idAlumno . " - " . $request->idGrupo);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
