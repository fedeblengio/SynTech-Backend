<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\grupos;

class gruposController extends Controller
{
    public function index()
    {
        return response()->json(grupos::all());
    }

    public function create(Request $request)
    {
        $gruposDB = grupos::where('nombreGrupo', $request->nombreGrupo)->first();

    
        if ($gruposDB) {
            return response()->json(['error' => 'Forbidden'], 403);
        } else {
            $gruposDB = new grupos;
            $gruposDB->nombreGrupo = $request->nombreGrupo;
            $gruposDB->horarioGrupo = $request->horarioGrupo;
            $gruposDB->save();

            return response()->json(['status' => 'Success'], 200);
        }
    }

    public function show(request $request)
    {
        $gruposDB = grupos::where('nombreGrupo', $request->nombreGrupo)->first();
        return response()->json($gruposDB);
    }

    public function destroy(request $request)
    {
        $gruposDB = grupos::where('nombreGrupo', $request->nombreGrupo)->first();

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
        $gruposDB = grupos::where('nombreGrupo', $request->nombreGrupo)->first();
        $gruposDB->horarioGrupo = $request->horarioGrupo;
        $gruposDB->save();
        return response()->json(['status' => 'Success'], 200);
    } catch (\Throwable $th) {
        return response()->json(['status' => 'Bad Request'], 400);
    }
    }



}
