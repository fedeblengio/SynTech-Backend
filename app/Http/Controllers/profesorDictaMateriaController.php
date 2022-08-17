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

    public function index(Request $request)
    {

        $variable = $request->idProfesor;
        $resultado = DB::select(
            DB::raw('SELECT id , nombre  FROM (SELECT * from materias) as A LEFT JOIN (SELECT * FROM profesor_dicta_materia WHERE idProfesor=:variable) as B ON A.id = B.idMateria WHERE B.idMateria IS NULL;'),
            array('variable' => $variable)
        );
        return response()->json($resultado);
    }
    public function todosProfesorSegunMateria(Request $request)
    {
        return   DB::table('profesor_dicta_materia')
            ->select('profesor_dicta_materia.idProfesor', 'usuarios.nombre', 'usuarios.email', 'materias.id as idMateria', 'materias.nombre as materia')
            ->join('usuarios', 'profesor_dicta_materia.idProfesor', '=', 'usuarios.id')
            ->join('materias', 'materias.id', '=', 'profesor_dicta_materia.idMateria')
            ->where('materias.id', $request->idMateria)
            ->whereNull('profesor_dicta_materia.deleted_at')
            ->get();
    }
    public function listarProfesores(Request $request)
    {
        $variable = $request->idMateria;
        $resultado = DB::select(
            DB::raw('SELECT A.id , A.nombre  FROM (SELECT profesores.id , usuarios.nombre from profesores JOIN usuarios ON profesores.Cedula_Profesor = usuarios.id WHERE usuarios.deleted_at IS NULL) as A LEFT JOIN (SELECT * FROM profesor_dicta_materia WHERE idMateria=:variable AND deleted_at IS NULL) as B ON A.id = B.idProfesor WHERE B.idProfesor IS NULL ;'),
            array('variable' => $variable)
        );
        return response()->json($resultado);

        $a =  DB::table('usuarios')
            ->select('usuarios.id', 'usuarios.nombre')
            ->leftJoin('profesor_dicta_materia', 'profesor_dicta_materia.idProfesor', '=', 'usuarios.id')
            ->where('profesor_dicta_materia.idMateria', $request->idMateria)
            ->get();

            /* $a =  DB::table('usuarios') */
            /* ->select('*') */
            /* ->leftJoin('profesor_dicta_materia', 'profesor_dicta_materia.idProfesor', '=', 'usuarios.id')
            ->where('profesor_dicta_materia.idMateria', $request->idMateria) */
          /*   ->get();
 */
        return response()->json($a);
    }

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
                DB::table('profesor_dicta_materia')
                ->where('idMateria', $idMateria)
                ->where('idProfesor', $idProfesor)
                ->update(['deleted_at' => null]);
                RegistrosController::store("PROFESOR MATERIA",$token,"ACTIVATE",$idProfesor);
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Materia Existe'], 416);
        } else {

            $agregarProfesorMateria = new profesor_dicta_materia;
            $agregarProfesorMateria->idMateria = $idMateria;
            $agregarProfesorMateria->idProfesor = $idProfesor;
            $agregarProfesorMateria->save();
            RegistrosController::store("PROFESOR MATERIA",$token,"CREATE",$idProfesor);
            return response()->json(['status' => 'Success'], 200);
        }
    }




    public function show(Request $request)
    {
        return response()->json(profesor_dicta_materia::all()->where('idProfesor', $request->idProfesor));
    }


    public function update(Request $request, $id)
    {
    }


    public function destroy(Request $request)
    {
        try {
            /* DB::delete('delete from profesor_dicta_materia where idMateria="' . $request->idMateria . '" AND idProfesor="' . $request->idProfesor . '" ;'); */
            $perteneceMateria = profesor_dicta_materia::where('idMateria', $request->idMateria)->where('idProfesor', $request->idProfesor)->first();
            $perteneceMateria->delete();
            self::eliminarProfesorGrupo($request);
            RegistrosController::store("PROFESOR MATERIA",$request->header('token'),"DELETE",$request->idMateria." - ".$request->idProfesor);
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
            RegistrosController::store("MATERIA GRUPOS",$request->header('token'),"DELETE",$request->idMateria." - ".$request->idProfesor);
    }
}
