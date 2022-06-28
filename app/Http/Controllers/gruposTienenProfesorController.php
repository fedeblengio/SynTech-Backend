<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profesor_dicta_materia;
use App\Models\grupos_tienen_profesor;
use App\Models\foro;
use App\Models\profesorEstanGrupoForo;
use Illuminate\Support\Facades\Storage;
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

        $profesorGrupo = DB::table('grupos_tienen_profesor')
            ->select('*')
            ->where('idMateria', $request->idMateria)
            ->where('idGrupo', $request->idGrupo)
            ->first();
        if ($profesorGrupo) {
            if ($profesorGrupo->deleted_at) {
                DB::table('grupos_tienen_profesor')
                    ->where('idMateria', $request->idMateria)
                    ->where('idGrupo', $request->idGrupo)
                    ->update(['deleted_at' => null]);
                self::actualizarForoProfesor($request);
            }
        } else {
            $agregarProfesorGrupo = new grupos_tienen_profesor;
            $agregarProfesorGrupo->idMateria = $request->idMateria;
            $agregarProfesorGrupo->idProfesor = $request->idProfesor;
            $agregarProfesorGrupo->idGrupo = $request->idGrupo;
            $agregarProfesorGrupo->save();
            self::crearForo($request);
            return response()->json(['status' => 'Success'], 200);
        }

        return response()->json(['status' => 'Not Acceptable'], 406);
    }

    public function actualizarForoProfesor($request)
    {
        $profesorForo = profesorEstanGrupoForo::where('idGrupo', $request->idGrupo)->where('idMateria', $request->idMateria)->first();
        $profesorForo->idProfesor = $request->idProfesor;
        $profesorForo->save();
    }


    public function traerMateriasSinGrupo($request)
    {

        $variable = $request->idGrupo;
        $resultado = DB::select(
            DB::raw('SELECT A.id , A.nombre  FROM (SELECT * from materias WHERE deleted_at is NULL) as A LEFT JOIN (SELECT * FROM grupos_tienen_profesor WHERE idGrupo=:variable) as B ON A.id = B.idMateria WHERE B.idMateria IS NULL;'),
            array('variable' => $variable)
        );
        return response()->json($resultado);
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
                DB::delete('delete from profesor_estan_grupo_foro where idForo=' . $request->idForo . ';');
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
                DB::delete('delete from foros where id=' . $request->idForo . ';');
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
                /* DB::delete('delete from grupos_tienen_profesor where idMateria="' . $datos->idMateria . '" AND idProfesor="' . $datos->idProfesor . '" AND idGrupo="' . $datos->idGrupo . '"   ;'); */
                $datos->delete();
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function listarIntegrantesDeUnGrupo(Request $request)
    {
        $profesores = DB::table('grupos_tienen_profesor')
            ->select('usuarios.id AS idProfesor', 'usuarios.nombre AS nombreProfesor', 'usuarios.imagen_perfil', 'materias.id AS idMateria', 'materias.nombre AS nombreMateria')
            ->join('materias', 'materias.id', '=', 'grupos_tienen_profesor.idMateria')
            ->join('usuarios', 'usuarios.id', '=', 'grupos_tienen_profesor.idProfesor')
            ->where('grupos_tienen_profesor.idGrupo', '=', $request->idGrupo)
            ->get();

        foreach ($profesores as $p) {
            $p->imagen_perfil = self::traerArchivos($p->imagen_perfil);
        }
        $alumnos = DB::table('alumnos_pertenecen_grupos')
            ->select('usuarios.id AS idAlumno', 'usuarios.nombre AS nombreAlumno', 'usuarios.imagen_perfil')
            ->join('usuarios', 'usuarios.id', '=', 'alumnos_pertenecen_grupos.idAlumnos')
            ->where('alumnos_pertenecen_grupos.idGrupo', '=', $request->idGrupo)
            ->get();

        foreach ($alumnos as $a) {
            $a->imagen_perfil = self::traerArchivos($a->imagen_perfil);
        }
        return response()->json(["profesores" => $profesores, "alumnos" => $alumnos]);
    }

    public function traerArchivos($nombre_archivo)
    {
        return base64_encode(Storage::disk('ftp')->get($nombre_archivo));
    }
}
