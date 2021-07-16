<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\materia;

class agregarMateriaController extends Controller
{
    public function index()
    {
        return response()->json(materia::all());
    }

    public function store(Request $request)
    {
        $existeMateria = materia::where('nombre', $request->nombreMateria)->first();
        try {
            if (!$existeMateria) {
                $agregarMateria = new materia;
                $agregarMateria->nombre = $request->nombreMateria;
                $agregarMateria->save();
                return response()->json(['status' => 'Success'], 200);
            } else {
                return response()->json(['status' => 'Materia Existe'], 416);
            }
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function show(Request $request)
    {
        return response()->json(materia::where('id', $request->idMateria)->get());
    }

 
    public function update(Request $request)
    {
        try {
          $modificarMateria = materia::where('id', $request->idMateria)->first(); 
            $modificarMateria->nombre = $request->nuevoNombre;
            $modificarMateria->save();
            return response()->json(['status' => 'Success'], 200);
         } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
    public function destroy(Request $request)
    {
        $eliminarMateria = materia::where('id', $request->idMateria)->first();
        try {
            $eliminarMateria->delete();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
