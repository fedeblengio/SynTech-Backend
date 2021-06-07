<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profesor_dicta_materia;
use App\Models\grupos_tienen_profesor;
use App\Models\materia;
use App\Models\profesores;
class gruposTienenProfesorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            $profesorTieneMateria=profesor_dicta_materia::where('idMateria',$request->idMateria)->where('idProfesor',$request->idProfesor)->first();
        try {

            if($profesorTieneMateria){
                $agregarProfesorGrupo = new grupos_tienen_profesor;
                $agregarProfesorGrupo->idMateria = $profesorTieneMateria->idMateria;
                $agregarProfesorGrupo->idProfesor = $profesorTieneMateria->idProfesor;
                $agregarProfesorGrupo->idGrupo = $request->idGrupo;
                $agregarProfesorGrupo->save();
                return response()->json(['status' => 'Success'], 200);
            }else{
                return response()->json(['status' => 'Bad Request'], 400);
            }

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
    public function show($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
