<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\materia;

class agregarMateriaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return response()->json(materia::all());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
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



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request)
    {
        return response()->json(materia::where('nombre', $request->nombreMateria)->get());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $modificarMateria = materia::where('nombre', $request->nombreMateria)->first();
       
        try {
            $modificarMateria->nombre = $request->nuevoNombre;
            $modificarMateria->save();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }



    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $eliminarMateria = materia::where('nombre', $request->nombreMateria)->first();
        try {
            $eliminarMateria->delete();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
