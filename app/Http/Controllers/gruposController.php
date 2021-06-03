<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\grupos;
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
            return response()->json(['error' => 'Forbidden'], 403);
        } else {
            $gruposDB = new grupos;
            $gruposDB->idGrupo = $request->idGrupo;
            $gruposDB->nombreCompleto = $request->nombreCompleto;
	    $gruposDB->anioElectivo=Carbon::now()->format('Y');
            $gruposDB->save();

            return response()->json(['status' => 'Success'], 200);
        }
    }

    public function show(request $request)
    {
        $AlumnosDeGrupo = DB::table('listar_alumnos_sin_grupos')->get();
        return response()->json($AlumnosDeGrupo);
    }

    public function destroy(request $request)
    {
        $gruposDB = grupos::where('idGrupo', $request->idGrupo)->first();

        try {
            $gruposDB->delete();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function update(request $request)
    {
        try {
        $gruposDB = grupos::where('idGrupo', $request->idGrupo)->first();
        $gruposDB->nombreCompleto = $request->nombreCompleto;
        $gruposDB->save();
        return response()->json(['status' => 'Success'], 200);
    } catch (\Throwable $th) {
        return response()->json(['status' => 'Bad Request'], 400);
    }
    }



}
