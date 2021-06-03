<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\usuarios;
use App\Models\grupos;
use App\Models\usuarioGrupos;
class agregarUsuarioGrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function listarAlumnos()
    {
        return response()->json(DB::table('listar_alumnos_sin_grupo')->get());
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $agregarUserGrupo= new usuarioGrupos;
            $agregarUserGrupo->idGrupo = $request->idGrupo;
            $agregarUserGrupo->idAlumno = $request->idAlumno;
            $agregarUserGrupo->Cedula = $request->idAlumno;
            $agregarUserGrupo->save();
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
        $listarAlumnosGrupo=grupos::where('idGrupo', $request->idGrupo);

        return $listarAlumnosGrupo->idAlumno;
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
