<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\materia;
use App\Models\profesores;
use App\Models\profesor_dicta_materia;
use App\Http\Controllers\RegistrosController;
use App\Models\usuarios;

class profesorDictaMateriaController extends Controller
{
    public static function activarProfesorMateria($idMateria, $idProfesor, $token): \Illuminate\Http\JsonResponse
    {
        DB::table('profesor_dicta_materia')
            ->where('idMateria', $idMateria)
            ->where('idProfesor', $idProfesor)
            ->update(['deleted_at' => null]);
        RegistrosController::store("PROFESOR MATERIA", $token, "ACTIVATE", $idProfesor);
        return response()->json(['status' => 'Success'], 200);
    }


    public static function agregarProfesorMateria($idMateria, $idProfesor, $token): \Illuminate\Http\JsonResponse
    {
        $agregarProfesorMateria = new profesor_dicta_materia;
        $agregarProfesorMateria->idMateria = $idMateria;
        $agregarProfesorMateria->idProfesor = $idProfesor;
        $agregarProfesorMateria->save();
        RegistrosController::store("PROFESOR MATERIA", $token, "CREATE", $idProfesor);
        return response()->json(['status' => 'Success'], 200);
    }

    
  /* 
    public function index(Request $request)
    {
        $resultado = DB::table('profesores')
            ->select('profesores.id', 'usuarios.nombre')
            ->join('usuarios', 'profesores.Cedula_Profesor', '=', 'usuarios.id')
            ->leftJoin('profesor_dicta_materia', function ($join) use ($request) {
                $join->on('profesores.id', '=', 'profesor_dicta_materia.idProfesor')
                    ->where('profesor_dicta_materia.idMateria', '=', $request->idMateria);
            })
            ->whereNull('profesor_dicta_materia.idProfesor')
            ->whereNull('profesor_dicta_materia.deleted_at')
            ->whereNull('usuarios.deleted_at') 
            ->get();
          
          return response()->json($resultado);
    } */

    public  function agregarListaDeProfesoresMateria(Request $request)
    {

        try {
            foreach ($request->profesores as $p) {
                self::store($request->idMateria, $p, $request->header('token'));
            }

            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public static function store($idMateria, $idProfesor, $token)
    {

        $perteneceMateria = DB::table('profesor_dicta_materia')
            ->select('*')
            ->where('idMateria', $idMateria)
            ->where('idProfesor', $idProfesor)
            ->first();
        if ($perteneceMateria) {
            if ($perteneceMateria->deleted_at) {
                return self::activarProfesorMateria($idMateria, $idProfesor, $token);
            }
            return response()->json(['status' => 'Materia Existe'], 416);
        } else {

            return self::agregarProfesorMateria($idMateria, $idProfesor, $token);
        }
    }

    public function materiasNoPertenecenProfesor($id)
    {

    $profesor = profesores::find($id);
    $resultado = materia::whereDoesntHave('profesores', function($query) use ($profesor) 
    {$query->where('idProfesor', $profesor->id);})
    ->get();

    return response()->json($resultado); 
    }

    public function destroy(Request $request)
    {
        try {
            $perteneceMateria = profesor_dicta_materia::where('idMateria', $request->idMateria)->where('idProfesor', $request->idProfesor)->first();
            $perteneceMateria->delete();
            self::eliminarProfesorGrupo($request);
            RegistrosController::store("PROFESOR MATERIA", $request->header('token'), "DELETE", $request->idMateria . " - " . $request->idProfesor);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function eliminarProfesorGrupo($request)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idMateria', $request->idMateria)
            ->where('idProfesor', $request->idProfesor)
            ->delete();
        RegistrosController::store("MATERIA GRUPOS", $request->header('token'), "DELETE", $request->idMateria . " - " . $request->idProfesor);
    }
}
