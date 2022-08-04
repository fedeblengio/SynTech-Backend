<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\usuarios;
use App\Models\grupos;
use Carbon\Carbon;
use App\Models\alumnos_pertenecen_grupos;

class agregarUsuarioGrupoController extends Controller
{
    public function index(Request $request)
    {
     

        $variable = $request->idGrupo;
        $resultado = DB::select(
            DB::raw('SELECT A.id , A.nombre, A.email  FROM (SELECT * from usuarios WHERE deleted_at is NULL) as A JOIN (SELECT * FROM alumnos) as B ON A.id = B.id LEFT JOIN (SELECT * FROM alumnos_pertenecen_grupos WHERE idGrupo=:variable AND deleted_at IS NULL) as C ON A.id = C.idAlumnos WHERE C.idGrupo IS NULL;'),
            array('variable' => $variable)
        );

       /*  $alumnoseliminados = DB::table('alumnos_pertenecen_grupos')
            ->select('usuarios.id', 'usuarios.nombre', 'usuarios.email')
            ->join('usuarios', 'usuarios.id', '=', 'alumnos_pertenecen_grupos.idAlumnos')
            ->where('alumnos_pertenecen_grupos.idGrupo', '=', $request->idGrupo)
            ->where('alumnos_pertenecen_grupos.deleted_at')
            ->get();


            foreach ($alumnoseliminados as $a) {
                array_push($resultado,$a);
            } */




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
                DB::table('alumnos_pertenecen_grupos')
                    ->where('idAlumnos', $request->idAlumno)
                    ->where('idGrupo', $request->idGrupo)
                    ->update(['deleted_at' => null]);
                return response()->json(['status' => 'Success'], 200);
            }
           
        } else {
            $agregarAlumnoGrupo = new alumnos_pertenecen_grupos;
            $agregarAlumnoGrupo->idGrupo = $request->idGrupo;
            $agregarAlumnoGrupo->idAlumnos = $request->idAlumno;
            $agregarAlumnoGrupo->save();
            return response()->json(['status' => 'Success'], 200);
        }
    }
    public function destroy(request $request)
    {
        try {
            DB::table('alumnos_pertenecen_grupos')
                ->where('idAlumnos', $request->idAlumno)
                ->where('idGrupo', $request->idGrupo)
                ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
