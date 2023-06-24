<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\profesores;
use App\Http\Controllers\usuariosController;
use App\Models\usuarios;
use App\Services\Files;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
class ProfesorController extends Controller
{
    
    public function index(Request $request)
    {  
        if($request->eliminados){
            $profesoresEliminados = DB::table('usuarios')
            ->select('*')
            ->where('deleted_at', '!=', null)
            ->where('ou', 'Profesor')
            ->get();
            return response()->json($profesoresEliminados);
        }

        if(empty($request->idMateria)){
            return usuarios::where('ou', 'Profesor')->orderBy('created_at','desc')->get();
        }

        return usuarios::where('ou', 'Profesor')
               ->join('profesor_dicta_materia', 'profesor_dicta_materia.idProfesor', '=', 'usuarios.id')
               ->where('profesor_dicta_materia.idMateria','=',$request->idMateria)
               ->get();
    }

    public function update(Request $request, $id)
    {
        $profesor = profesores::findOrFail($id);

        $profesor->materia()->sync($request->materias);
        $usuarioController = new usuariosController();
        return $usuarioController->update($request, $id);
        
    }

    public function show($id)
    {
        $profesor = profesores::findOrFail($id)->load('materia','usuario');
        if(App::environment(['production', 'local'])){
        $filesService = new Files();
        $profesor->usuario['imagen_perfil'] = $filesService->getImage($profesor->usuario['imagen_perfil']);
        }
        return $profesor;
    }

    public function importFromCSV(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 400);
        }
        try {
            $file = $request->file('file');
            $usuarioController = new usuariosController();
            $usuarioController->importFromCSV($file, 'Profesor');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['message' => 'CSV file imported successfully']);
    }
}
