<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\usuarios;
use App\Models\grupos;
use App\Models\alumnos_pertenecen_grupos;
class agregarUsuarioGrupoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
       

            $alumnos_sin_grupo = DB::table('alumnos')
            ->select('usuarios.nombre','usuarios.username','usuarios.email')
            ->join('usuarios', 'usuarios.username', '=', 'alumnos.idAlumnos')
            ->leftJoin('alumnos_pertenecen_grupos', 'alumnos.idAlumnos', '=', 'alumnos_pertenecen_grupos.idAlumnos')
            ->whereNull('alumnos_pertenecen_grupos.idAlumnos')
            ->get();

            return response()->json($alumnos_sin_grupo);

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
            $agregarAlumnoGrupo= new alumnos_pertenecen_grupos;
            $agregarAlumnoGrupo->idGrupo = $request->idGrupo;
            $agregarAlumnoGrupo->idAlumnos = $request->idAlumnos;
            $agregarAlumnoGrupo->save();
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
  

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
  

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(request $request)
    {
        

        try {
            DB::delete('delete from alumnos_pertenecen_grupos where idAlumnos="'.$request->idAlumnos.'";');
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
