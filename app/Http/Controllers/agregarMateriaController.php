<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\materia;
use Carbon\Carbon;

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

                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Materia Existe'], 416);
        } else {
            $agregarMateria = new materia;
            $agregarMateria->nombre = $request->nombreMateria;
            $agregarMateria->save();
            return response()->json(['status' => 'Success'], 200);
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
            $modificarMateria->nombre = $request->nuevoNombre;
            $modificarMateria->save();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function destroy(Request $request)
    {
        $eliminarMateria = materia::where('id', $request->idMateria)->first();
        try {
            $eliminarMateria->delete();
            self::eliminarMateriaProfesor($request);
            self::eliminarMateriaGrupo($request);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function eliminarMateriaProfesor($request)
    {
        DB::table('profesor_dicta_materia')
            ->where('idMateria', $request->idMateria)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
    }
    public function eliminarMateriaGrupo($request)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idMateria', $request->idMateria)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
    }
}
