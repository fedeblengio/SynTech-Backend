<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\materia;
use App\Models\profesores;
use App\Models\profesor_dicta_materia;

class profesorDictaMateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        /*        $sql = "SELECT id , nombre  FROM (SELECT * from materias) as A LEFT JOIN (SELECT * FROM profesor_dicta_materia WHERE idProfesor=51717999)  as B ON A.id = B.idMateria WHERE B.idMateria IS NULL;";
        $resultado = DB::query($sql);

        return response()->json($resultado); */
        return response()->json(profesor_dicta_materia::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $idMateria = materia::where('nombre', $request->nombreMateria)->first();
        // $perteneceMateria=profesor_dicta_materia::all()->where(['idMateria', $idMateria],['idProfesor', $request->idProfesor]);
        try {
            $agregarProfesorMateria = new profesor_dicta_materia;
            $agregarProfesorMateria->idMateria = $idMateria->id;
            $agregarProfesorMateria->idProfesor = $request->idProfesor;
            $agregarProfesorMateria->save();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return response()->json(profesor_dicta_materia::all()->where('idProfesor', $request->idProfesor));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
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
