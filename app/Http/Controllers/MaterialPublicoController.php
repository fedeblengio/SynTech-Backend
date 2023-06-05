<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\material_publico;
use App\Models\archivos_material_publico;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RegistrosController;


class MaterialPublicoController extends Controller
{


    public function store(Request $request)
    {
        $request->validate([
            'titulo' => 'required|string|max:30',
            'mensaje' => 'required|string|max:30',
            'idUsuario' => 'required|string|max:8|min:8',
        ]);
        $nombreEncabezado = "encabezadoPredeterminado.jpg";
        $nombreEncabezado = $this->comprobacionEncabezado($request, $nombreEncabezado);

        $nuevaNoticia = $this->createMaterialPublico($request, $nombreEncabezado);

        $idDatos = DB::table('material_publicos')->orderBy('created_at', 'desc')->limit(1)->get('id');

        $this->subidaArchivo($request, $idDatos[0]);

        RegistrosController::store("PUBLICACION PUBLICA", $request->header('token'), "CREATE", $request->idUsuario);
        return response()->json($nuevaNoticia, 201);
    }

    public function index(Request $request)
    {


            $peticionSQL = DB::table('material_publicos')
            ->select('material_publicos.id', 'material_publicos.imgEncabezado', 'material_publicos.titulo AS titulo', 'material_publicos.mensaje AS mensaje', 'material_publicos.idUsuario', 'material_publicos.imgEncabezado', 'material_publicos.created_at AS fecha', 'usuarios.nombre AS nombreAutor')
            ->join('usuarios', 'usuarios.id', '=', 'material_publicos.idUsuario')
            ->orderBy('material_publicos.id', 'desc')
            ->take($request->limit)
            ->get();

        $dataResponse = array();


        foreach ($peticionSQL as $p) {
            $peticionSQLFiltrada = DB::table('archivos_material_publico')
                ->select('nombreArchivo AS archivo')
                ->where('idMaterialPublico', $p->id)
                ->distinct()
                ->get();
            if(!App::environment('testing')){
                $p->imgEncabezado = base64_encode(Storage::disk('ftp')->get($p->imgEncabezado));
            }
             
          
            $arrayArchivos = array();


            foreach ($peticionSQLFiltrada as $p2) {
                array_push($arrayArchivos, $p2->archivo);
            }


            $datos = [
                "id" => $p->id,
                "imagenEncabezado" => $p->imgEncabezado,
                "mensaje" => $p->mensaje,
                "titulo" => $p->titulo,
                "idUsuario" => $p->idUsuario,
                "nombreAutor" => $p->nombreAutor,
                "fecha" => $p->fecha
            ];

            $p = [
                "data" => $datos,
                "archivos" => $arrayArchivos,
            ];

            array_push($dataResponse, $p);
        }
        return response()->json($dataResponse);
    }

    public function traerArchivo(Request $request)
    {
        if(!App::environment('testing')){
          return Storage::disk('ftp')->get($request->archivo);
        }
    }

    public function destroy($id, Request $request)
    {   
        $materialPublico = material_publico::findOrFail($id);

        $arhivosMaterialPublico = archivos_material_publico::where('idMaterialPublico', $id)->get();
        foreach ($arhivosMaterialPublico as $p) {
            if(!App::environment('testing')){
                Storage::disk('ftp')->delete($p->nombreArchivo);
            }
            $arhivosId = archivos_material_publico::where('id', $p->id)->first();
            $arhivosId->delete();
        }
        try {
            $materialPublico->delete();
            RegistrosController::store("PUBLICACION PUBLICA", $request->header('token'), "DELETE", $materialPublico->idUsuario);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }


    public function createMaterialPublico(Request $request, string $nombreEncabezado)
    {
        $materialPublico = new material_publico;
        $materialPublico->idUsuario = $request->idUsuario;
        $materialPublico->titulo = $request->titulo;
        $materialPublico->mensaje = $request->mensaje;
        $materialPublico->imgEncabezado = $nombreEncabezado;
        $materialPublico->save();

        return $materialPublico;
    }


    public function comprobacionEncabezado(Request $request, string $nombreEncabezado)
    {
        if ($request->imagenEncabezado) {
            $nombreEncabezado = random_int(0, 1000000) . "_" . $request->nombreEncabezado;
            if(!App::environment('testing')){
                Storage::disk('ftp')->put($nombreEncabezado, fopen($request->imagenEncabezado, 'r+'));
            }
         
        }
        return $nombreEncabezado;
    }


    public function subidaArchivo(Request $request, $idDatos)
    {
        if ($request->archivos) {

            for ($i = 0; $i < count($request->nombresArchivo); $i++) {
                $nombreArchivo = random_int(0, 1000000) . "_" . $request->nombresArchivo[$i];
                if(!App::environment('testing')){
                Storage::disk('ftp')->put($nombreArchivo, fopen($request->archivos[$i], 'r+'));
                }
                $archivosForo = new archivos_material_publico;
                $archivosForo->idMaterialPublico = $idDatos->id;
                $archivosForo->nombreArchivo = $nombreArchivo;
                $archivosForo->save();
            }
        }
    }
}
