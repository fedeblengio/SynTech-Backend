<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\material_publico;
use App\Models\archivos_material_publico;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\RegistrosController;
use Carbon\Carbon;


class MaterialPublicoController extends Controller
{


    public function store(Request $request)
    {
        $nombreEncabezado = "encabezadoPredeterminado.jpg";
        if($request->imagenEncabezado){
            $nombreEncabezado = random_int(0,1000000)."_".$request->nombreEncabezado;
            Storage::disk('ftp')->put($nombreEncabezado, fopen($request->imagenEncabezado, 'r+'));
        }
           
        $materialPublico = new material_publico;
        $materialPublico->idUsuario = $request->idUsuario;
        $materialPublico->titulo = $request->titulo;
        $materialPublico->mensaje = $request->mensaje;
        $materialPublico->imgEncabezado = $nombreEncabezado;
        $materialPublico->save();

        $idDatos = DB::table('material_publicos')->orderBy('created_at', 'desc')->limit(1)->get('id');

        if ($request->archivos) {
            
            for ($i=0; $i < count($request->nombresArchivo); $i++){
                $nombreArchivo = random_int(0,1000000)."_".$request->nombresArchivo[$i];
                Storage::disk('ftp')->put($nombreArchivo, fopen($request->archivos[$i], 'r+'));
                $archivosForo = new archivos_material_publico;
                $archivosForo->idMaterialPublico = $idDatos[0]->id;
                $archivosForo->nombreArchivo = $nombreArchivo;
                $archivosForo->save();
            }
    }

        RegistrosController::store("PUBLICACION PUBLICA", $request->header('token'), "CREATE", $request->idUsuario);
        return response()->json(['status' => 'Success'], 200);
    }

    public function index(Request $request)
    {
        $peticionSQL = DB::table('bedelias')
            ->select('material_publicos.id', 'material_publicos.imgEncabezado', 'material_publicos.titulo AS titulo', 'material_publicos.mensaje AS mensaje', 'material_publicos.idUsuario', 'material_publicos.created_at AS fecha', 'usuarios.nombre AS nombreAutor')
            ->join('usuarios', 'usuarios.id', '=', 'bedelias.id')
            ->join('material_publicos', 'material_publicos.idUsuario', '=', 'bedelias.id')
            ->orderBy('id', 'desc')
            ->take($request->limit)
            ->get();

        $dataResponse = array();


        foreach ($peticionSQL as $p) {
            $peticionSQLFiltrada = DB::table('archivos_material_publico')
                ->select('nombreArchivo AS archivo')
                ->where('idMaterialPublico', $p->id)
                ->distinct()
                ->get();

            $arrayArchivos = array();
            $arrayImagenes = array();
            $postAuthor = $p->idUsuario;
            $imgPerfil = DB::table('usuarios')
                ->select('imagen_perfil')
                ->where('id', $postAuthor)
                ->get();

            $img = base64_encode(Storage::disk('ftp')->get($imgPerfil[0]->imagen_perfil));

            $imgEncabezado = base64_encode(Storage::disk('ftp')->get($p->imgEncabezado));

            foreach ($peticionSQLFiltrada as $p2) {

                $resultado = strpos($p2->archivo, ".pdf");
                if ($resultado) {
                    array_push($arrayArchivos, $p2->archivo);
                } else {
                    array_push($arrayImagenes, base64_encode(Storage::disk('ftp')->get($p2->archivo)));
                }
            }

            $datos = [
                "id" => $p->id,
                "profile_picture" => $img,
                "imagenEncabezado" => $imgEncabezado,
                "mensaje" => $p->mensaje,
                "titulo" => $p->titulo,
                "idUsuario" => $p->idUsuario,
                "nombreAutor" => $p->nombreAutor,
                "fecha" => $p->fecha
            ];

            $p = [
                "data" => $datos,
                "archivos" => $arrayArchivos,
                "imagenes" => $arrayImagenes,
            ];

            array_push($dataResponse, $p);
        }
        return response()->json($dataResponse);
    }

    public function traerArchivo(Request $request)
    {
        return Storage::disk('ftp')->get($request->archivo);
    }

    public function destroy(Request $request)
    {

        $materialPublico = material_publico::where('id', $request->id)->first();
        $arhivosMaterialPublico = archivos_material_publico::where('idMaterialPublico', $request->id)->get();
        foreach ($arhivosMaterialPublico as $p) {
            Storage::disk('ftp')->delete($p->nombreArchivo);
            $arhivosId = archivos_material_publico::where('id', $p->id)->first();
            $arhivosId->delete();
        }
        try {
            $materialPublico->delete();
            RegistrosController::store("PUBLICACION PUBLICA", $request->header('token'), "DELETE", $request->idUsuario);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
