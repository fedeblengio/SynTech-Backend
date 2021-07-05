<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\grupos;
use App\Models\alumnos_pertenecen_grupos;
use Carbon\Carbon;

class gruposController extends Controller
{
    public function index()
    {
        return response()->json(grupos::all());
    }

    public function create(Request $request)
    {
        $gruposDB = grupos::where('idGrupo', $request->idGrupo)->first();


        if ($gruposDB) {
            return response()->json(['error' => 'Forbidden'], 416);
        } else {
            $gruposDB = new grupos;
            $gruposDB->idGrupo = $request->idGrupo;
            $gruposDB->nombreCompleto = $request->nombreCompleto;
            $gruposDB->anioElectivo = Carbon::now()->format('Y');
            $gruposDB->save();

            return response()->json(['status' => 'Success'], 200);
        }
    }

    public function show(request $request)
    {
        return response()->json(grupos::where('idGrupo', $request->idGrupo)->first());
    }

    public function destroy(request $request)
    {
        $existe = grupos::where('idGrupo', $request->idGrupo)->first();

        try {
            if ($existe) {
                DB::delete('delete from grupos where idGrupo="' . $request->idGrupo . '" ;');
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function update(request $request)
    {
        $existe = grupos::where('idGrupo', $request->idGrupo)->first();
      try {
            if ($existe) {
                if($request->nuevoGrupo){
                    DB::update('UPDATE grupos SET idGrupo="' . $request->nuevoGrupo . '" ,  nombreCompleto="' . $request->nuevoNombreCompleto . '" WHERE idGrupo="' . $request->idGrupo . '";');
                    return response()->json(['status' => 'Success'], 200);
                }else{
                    DB::update('UPDATE grupos SET idGrupo="' . $request->idGrupo . '" ,  nombreCompleto="' . $request->nuevoNombreCompleto . '" WHERE idGrupo="' . $request->idGrupo . '";');
                    return response()->json(['status' => 'Success'], 200);
                }
               
            }
            return response()->json(['status' => 'Bad Request'], 400);
       } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
       }
    }
}
