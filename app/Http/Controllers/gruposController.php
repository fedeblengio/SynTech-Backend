<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\grupos;
use App\Models\alumnos_pertenecen_grupos;
use App\Models\alumnos;
use App\Models\materia;
use App\Models\profesores;
use App\Models\usuarios;
use Carbon\Carbon;
use App\Http\Controllers\RegistrosController;
use App\Models\grupos_tienen_profesor;
use App\Models\profesor_dicta_materia;

class gruposController extends Controller
{
    public function index()
    {

        return response()->json(grupos::all());
    }

    public function store(Request $request)
    {

        $request->validate([
            'idGrupo' => 'required|string',
            'anioElectivo' => 'required|max:4',
            'grado_id' => 'required|integer',
        ]);
        $grupo = grupos::where('idGrupo', $request->idGrupo)->first();
        if (empty($grupo)) {
            return $this->crearGrupo($request);
        }
        return response()->json(['error' => 'Grupo Existente'], 401);

    }

    public function show($id)
    {
        $grupo = grupos::where('idGrupo', $id)->first()->load('grado.materias');
        $profesores = DB::table('grupos')
            ->select('usuarios.id', 'usuarios.nombre')
            ->join('grupos_tienen_profesor', 'grupos_tienen_profesor.idGrupo', '=', 'grupos.idGrupo')
            ->join('usuarios', 'usuarios.id', '=', 'grupos_tienen_profesor.idProfesor')
            ->where('grupos.idGrupo', $id)
            ->get();
        $alumnos = DB::table('grupos')
            ->select('usuarios.id', 'usuarios.nombre')
            ->join('alumnos_pertenecen_grupos', 'alumnos_pertenecen_grupos.idGrupo', '=', 'grupos.idGrupo')
            ->join('usuarios', 'usuarios.id', '=', 'alumnos_pertenecen_grupos.idAlumnos')
            ->where('grupos.idGrupo', $id)
            ->get();

        return response()->json(['grupo' => $grupo,'profesores' => $profesores, 'alumnos' => $alumnos]);

        return response()->json($alumnos);
    
    }

    public function eliminarProfesorGrupo($id, $idProfesor, Request $request)
    {
        $profesorGrupo = grupos_tienen_profesor::where('idGrupo', $id)->where('idProfesor', $idProfesor)->first();
        if ($profesorGrupo) {
            $profesorGrupo->delete();
            RegistrosController::store("GRUPO", $request->header('token'), "DELETE", $idProfesor . " - " . $id);
            return response()->json(['status' => 'Success'], 200);
           
            
        }
        return response()->json(['status' => 'Bad Request'], 400);
    }

    public function eliminarAlumnoGrupo($id, $idAlumno, Request $request)
    {
      $alumnoGrupo = alumnos_pertenecen_grupos::where('idGrupo', $id)->where('idAlumnos', $idAlumno)->first();
        if ($alumnoGrupo) {
            $alumnoGrupo->delete();
            RegistrosController::store("GRUPO", $request->header('token'), "DELETE", $idAlumno . " - " . $id);
            return response()->json(['status' => 'Success'], 200);     
        }
        return response()->json(['status' => 'Bad Request'], 400);
    }


    public function destroy(Request $request, $id)
    {

        $grupo = grupos::where('idGrupo', $id)->first();
        if ($grupo) {
            self::eliminarProfesoresGrupo($request, $id);
            self::eliminarAlumnosGrupo($request, $id);
            RegistrosController::store("GRUPO", $request->header('token'), "DELETE", $request->idGrupo);
            $grupo->delete();
            return response()->json(['status' => 'Success'], 200);
        }
        return response()->json(['status' => 'Bad Request'], 400);
    }

    public function eliminarProfesoresGrupo($request, $id)
    {
        $gruposProfesor = grupos_tienen_profesor::where('idGrupo', $id)->get();
        $gruposProfesor->each(function ($gruposProfesor) {
            $gruposProfesor->delete();
        });
        RegistrosController::store("GRUPO PROFESOR", $request->header('token'), "DELETE", $request->idGrupo);
    }

    public function eliminarAlumnosGrupo($request, $id)
    {
        $alumnoGrupo = alumnos_pertenecen_grupos::where('idGrupo', $id)->get();
        $alumnoGrupo->each(function ($alumnoGrupo) {
            $alumnoGrupo->delete();
        });
        RegistrosController::store("GRUPO ALUMNOS", $request->header('token'), "DELETE", $request->idGrupo);
    }

    public function alumnosNoPertenecenGrupo($id){
        $resultado = alumnos::whereNotIn('id', function($query) use ($id){
            $query->select('idAlumnos')->from('alumnos_pertenecen_grupos')->where('idGrupo', $id);
        })->pluck('id');
        $alumnos = usuarios::whereIn('id',$resultado)->get();
     
      
        return response()->json($alumnos);
    }

    public function profesoresNoPertenecenGrupo($id){

        $materiasConGrupo = grupos_tienen_profesor::where('idGrupo', $id)->pluck('idMateria');

        $materias = grupos::where('idGrupo', $id)->first()->grado->materias->pluck('id');

        $materiaSinGrupo = collect($materias)->diff($materiasConGrupo)->values();

        $final = profesor_dicta_materia::whereIn('idMateria', $materiaSinGrupo)
        ->select('usuarios.id as idProfesor', 'usuarios.nombre as nombreProfesor', 'materias.id as idMateria', 'materias.nombre as materia')
        ->join('materias', 'materias.id', '=', 'profesor_dicta_materia.idMateria')
        ->join('usuarios', 'usuarios.id', '=', 'profesor_dicta_materia.idProfesor')
        ->get();

        return response()->json($final);
    }


    public function update(request $request, $id)
    {
        $request->validate([
            'profesores' => 'array',
            'alumnos' => 'array',
            ]);
        $grupo = grupos::where('idGrupo', $id)->first();
            if ($grupo) {
                $grupo->fill($request->all());
                $grupo->save();
                $grupo->alumnos()->sync($request->alumnos);
                $grupo->profesores()->sync($request->profesores);
                RegistrosController::store("GRUPO", $request->header('token'), "UPDATE", self::modifiedValue($grupo));
                return response()->json($grupo->load('alumnos', 'profesores'), 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
    }

    public function modifiedValue($grupo)
    {
        if ($grupo->isDirty('idGrupo') && !$grupo->isDirty('nombreCompleto')) {
            return $grupo->idGrupo . "-" . $grupo->getOriginal('idGrupo');
        }
        if ($grupo->isDirty('nombreCompleto') && !$grupo->isDirty('idGrupo')) {
            return $grupo->nombreCompleto . "-" . $grupo->getOriginal('nombreCompleto');
        }
        if ($grupo->isDirty('idGrupo') && $grupo->isDirty('nombreCompleto')) {
            return "Grupo Completo modificado";
        }
    }


    public function crearGrupo(Request $request)
    {
        $grupo = new grupos();
        $grupo->idGrupo = $request->idGrupo;
        $grupo->anioElectivo = $request->anioElectivo;
        $grupo->grado_id = $request->grado_id;
        $grupo->save();
        RegistrosController::store("GRUPO", $request->header('token'), "CREATE", $request->idGrupo);
        return response()->json($grupo);
    }


}
