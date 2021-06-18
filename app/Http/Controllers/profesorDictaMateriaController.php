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
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $variable = $request->idProfesor;
             
        $resultado = DB::select( DB::raw('SELECT id , nombre  FROM (SELECT * from materias) as A LEFT JOIN (SELECT * FROM profesor_dicta_materia WHERE idProfesor=:variable) as B ON A.id = B.idMateria WHERE B.idMateria IS NULL;'),
    array('variable' => $variable));

        /* $r = DB::table('materias') 
        ->leftJoin(DB::table('profesor_dicta_materia')->where('idProfesor', '49895207'), 'materias.id', '=', 'profesor_dicta_materia.idMateria')
        ->whereNull('profesor_dicta_materia.idMateria')
        ->get();
         */
        

        return response()->json($resultado);

        

      /*   return response()->json(profesor_dicta_materia::all()); */
    }

    public function listarProfesores()
    {
        return response()->json(usuarios::all()->where('ou','Profesor'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $perteneceMateria=profesor_dicta_materia::where('idMateria', $request->idMateria)->where('idProfesor', $request->idProfesor)->first();
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
