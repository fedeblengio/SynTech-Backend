<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profesor_dicta_materia;
use App\Models\grupos_tienen_profesor;
use App\Models\foro;
use App\Models\usuarios;
use Illuminate\Support\Facades\DB;
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

  /*   public function crearForo($request){
        $newForo = new foro;
        $newForo->save();
        $idForo = DB::table('foros')->orderBy('created_at', 'desc')->limit(1)->get('id');
        

    } */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {

        return response()->json(grupos_tienen_profesor::all()->where('idGrupo', $request->idGrupo));
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
    public function destroy(Request $request)
    {
        $datos=grupos_tienen_profesor::where('idMateria', $request->idMateria)->where('idProfesor', $request->idProfesor)->where('idGrupo', $request->idGrupo)->first();
        
        try {
            if($datos){
                DB::delete('delete from grupos_tienen_profesor where idMateria="' . $datos->idMateria . '" AND idProfesor="' . $datos->idProfesor . '" AND idGrupo="' . $datos->idGrupo . '"   ;');
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        } 
    }
}
