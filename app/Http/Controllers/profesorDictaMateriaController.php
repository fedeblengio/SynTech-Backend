<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\materia;
use App\Models\profesores;
use App\Models\profesor_dicta_materia;
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

    public function listarProfesores()
    {
        return response()->json(usuarios::all()->where('ou', 'Profesor'));
    }

    public function store(Request $request)
    {

        $perteneceMateria = profesor_dicta_materia::where('idMateria', $request->idMateria)->where('idProfesor', $request->idProfesor)->first();
        if ($perteneceMateria) {
            return response()->json(['status' => 'Not Acceptable'], 406);
        } else {
            try {
                $agregarProfesorMateria = new profesor_dicta_materia;
                $agregarProfesorMateria->idMateria = $request->idMateria;
                $agregarProfesorMateria->idProfesor = $request->idProfesor;
                $agregarProfesorMateria->save();
                return response()->json(['status' => 'Success'], 200);
            } catch (\Throwable $th) {
                return response()->json(['status' => 'Bad Request'], 400);
            }
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
            DB::delete('delete from profesor_dicta_materia where idMateria="' . $request->idMateria . '" AND idProfesor="' . $request->idProfesor . '" ;');
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
