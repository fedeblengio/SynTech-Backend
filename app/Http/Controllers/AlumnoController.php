<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;
use App\Models\alumnos_pertenecen_grupos;
use App\Models\alumnos;
use App\Models\grupos;
use App\Models\usuarios;
use App\Services\Files;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Validator;
class AlumnoController extends Controller
{

    public function index(Request $request)
    {   
        if($request->eliminados){
           
            $alumnosEliminados = DB::table('usuarios')
            ->select('*')
            ->where('deleted_at', '!=', null)
            ->where('ou', 'Alumno')
            ->get();
            return response()->json($alumnosEliminados);
        }
      
        return usuarios::where('ou', 'Alumno')->orderBy('created_at','desc')->get();
    }

    public function show($id){

        $alumno = alumnos::findOrFail($id)->load('usuario');
        $alumno['grupos'] = $this->getGrupos($id);
        if(App::environment(['production', 'local'])){
        $filesService = new Files();
        $alumno->usuario['imagen_perfil'] = $filesService->getImage($alumno->usuario['imagen_perfil']);
        }
        return $alumno;
       
    }

    public function getGrupos($id){
        $grupos = alumnos_pertenecen_grupos::where('idAlumnos',$id )->pluck('idGrupo');
        
        return grupos::whereIn('idGrupo',$grupos)->get();
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'string',
            'apellido' => 'string',
            'email' => 'string',
            'genero' => 'string',
            'grupos' => 'array',
        ]);
        $alumno = alumnos::findOrFail($id);
        $usuarioController = new usuariosController();
        return $usuarioController->update($request, $id);
    }

    public function gruposNoPertenecenAlumno($id){
        $alumno = alumnos::findOrFail($id);
        $resultado = grupos::whereDoesntHave('alumnos', function($query) use ($alumno){
            $query->where('idAlumnos', $alumno->id);
        })->get();

        return response()->json($resultado);
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
            $usuarioController->importFromCSV($file, 'Alumno');
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
        return response()->json(['message' => 'CSV file imported successfully']);
    }
}
