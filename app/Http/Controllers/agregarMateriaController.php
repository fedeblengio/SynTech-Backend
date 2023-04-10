<?php

namespace App\Http\Controllers;

use App\Http\Controllers\RegistrosController;
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
       $request->validate([
            'nombre' => 'required|string',
        ]);

        $existeMateria = materia::where('nombre', $request->nombre)->first();
        if (empty($existeMateria)) {
            return response()->json($this->createMateria($request));
        }

        return response()->json(['status' => 'Materia Existente'], 400);
    }

    public function show($id)
    {
        return response()->json(materia::find($id)->load('profesores'));
    }

    public function update(Request $request,$id)
    {
        $request->validate([
            'nombre' => 'required|string',
        ]);
        $materia = materia::find($id);

        if($materia){
            $materia->fill($request->all());
            RegistrosController::store("MATERIA", $request->header('token'), "UPDATE", $materia->getOriginal('nombre') . " - " . $request->nombre);
            $materia->save();

            return response()->json($materia);
        }
        return response()->json(['status' => 'Bad Request'], 400);

    }
    public function destroy(Request $request,$id)
    {
        $eliminarMateria = materia::find($id);
        $nombreMateria = $eliminarMateria->nombre;
   
        try {
            self::eliminarMateriaProfesor($request, $nombreMateria, $id);
            self::eliminarMateriaGrupo($request, $nombreMateria, $id);
            $eliminarMateria->delete();
           
            RegistrosController::store("MATERIA", $request->header('token'), "DELETE", $nombreMateria);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function eliminarMateriaProfesor($request, $materia,$id)
    {
        DB::table('profesor_dicta_materia')
            ->where('idMateria', $id)
            ->update(['deleted_at' => Carbon::now()]);
        RegistrosController::store("MATERIA PROFESOR", $request->header('token'), "DELETE", $materia);
    }
    public function eliminarMateriaGrupo($request, $materia,$id)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idMateria', $id)
            ->delete();
        RegistrosController::store("MATERIA GRUPO", $request->header('token'), "DELETE", $materia);
    }

    public function createMateria($request)
    {
        $materia = new materia;
        $materia->nombre = $request->nombre;
        $materia->save();
        RegistrosController::store("MATERIA", $request->header('token'), "CREATE", $request->nombre);
        return $materia;
    }



}
