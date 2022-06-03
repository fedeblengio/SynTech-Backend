<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profesor_dicta_materia;
use App\Models\grupos_tienen_profesor;
use App\Models\foro;
use App\Models\profesorEstanGrupoForo;

use App\Models\usuarios;
use Illuminate\Support\Facades\DB;

class gruposTienenProfesorController extends Controller
{

    public function index(Request $request)
    {
        $variable = $request->idGrupo;
        $variable2 = $request->idProfesor;

        $resultado = DB::select(
            DB::raw('SELECT A.idMateria, materias.nombre materia , A.idProfesor username , usuarios.nombre nombre  FROM (SELECT * from profesor_dicta_materia WHERE idProfesor=:variable2) as A LEFT JOIN (SELECT * FROM grupos_tienen_profesor WHERE idGrupo=:variable) as B ON A.idMateria = B.idMateria JOIN materias ON A.idMateria=materias.id JOIN usuarios ON A.idProfesor=usuarios.id WHERE B.idMateria IS NULL;'),
            array('variable' => $variable, 'variable2' => $variable2)
        );

        return response()->json($resultado);
        return "Hola";
    }

    public function mostrarProfesorMateria()
    {

        $profesor_materia = DB::table('profesor_dicta_materia')
            ->select('usuarios.id AS cedulaProfesor', 'usuarios.nombre AS nombreProfesor', 'materias.id AS idMateria', 'materias.nombre AS nombreMateria')
            ->join('materias', 'materias.id', '=', 'profesor_dicta_materia.idMateria')
            ->join('usuarios', 'usuarios.id', '=', 'profesor_dicta_materia.idProfesor')
            ->get();

        return response()->json($profesor_materia);
    }

    public function store(Request $request)
    {
        $profesorGrupo = grupos_tienen_profesor::where('idMateria', $request->idMateria)->where('idProfesor', $request->idProfesor)->where('idGrupo', $request->idGrupo)->first();
        try {

            if (!$profesorGrupo) {
                $agregarProfesorGrupo = new grupos_tienen_profesor;
                $agregarProfesorGrupo->idMateria = $request->idMateria;
                $agregarProfesorGrupo->idProfesor = $request->idProfesor;
                $agregarProfesorGrupo->idGrupo = $request->idGrupo;
                $agregarProfesorGrupo->save();
                self::crearForo($request);
                return response()->json(['status' => 'Success'], 200);
            } else {
                return response()->json(['status' => 'Not Acceptable'], 406);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function crearForo($request)
    {
        $newForo = new foro;
        $newForo->informacion = $request->idGrupo . "-" . $request->idProfesor . "-" . $request->idMateria;
        $newForo->save();

        $idForo = DB::table('foros')->orderBy('created_at', 'desc')->limit(1)->get('id');

        $profesorEstanGrupoForo = new profesorEstanGrupoForo;
        $profesorEstanGrupoForo->idMateria = $request->idMateria;
        $profesorEstanGrupoForo->idProfesor = $request->idProfesor;
        $profesorEstanGrupoForo->idGrupo = $request->idGrupo;
        $profesorEstanGrupoForo->idForo = $idForo[0]->id;
        $profesorEstanGrupoForo->save();
    }

    public function show(Request $request)
    {

        return response()->json(grupos_tienen_profesor::all()->where('idGrupo', $request->idGrupo));
    }


    public function update(Request $request, $id)
    {
       
    }


    public function eliminarProfesorGrupoForo(Request $request)
    {
        $datos = profesorEstanGrupoForo::where('idForo', $request->idForo);

        try {
            if ($datos) {
                DB::delete('delete from profesor_estan_grupo_foro where idForo='.$request->idForo.';');
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function eliminarForo(Request $request)
    {
        $datos = foro::where('id', $request->idForo);

        try {
            if ($datos) {
                DB::delete('delete from foros where id='.$request->idForo.';');
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function destroy(Request $request)
    {
        $datos = grupos_tienen_profesor::where('idMateria', $request->idMateria)->where('idProfesor', $request->idProfesor)->where('idGrupo', $request->idGrupo)->first();

        try {
            if ($datos) {
                DB::delete('delete from grupos_tienen_profesor where idMateria="' . $datos->idMateria . '" AND idProfesor="' . $datos->idProfesor . '" AND idGrupo="' . $datos->idGrupo . '"   ;');
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
