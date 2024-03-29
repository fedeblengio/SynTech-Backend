<?php

namespace App\Http\Controllers;

use App\Models\Registros;

class RegistrosController extends Controller
{
    public function index()
    {
        return response()->json(Registros::all());
    }

    public  static function store($objeto, $token, $accion, $info)
    {
        try {
            $registros = new registros;
            $registros->idUsuario = json_decode(base64_decode($token))->username;
            $registros->app = "BACKOFFICE";
            $registros->accion = $accion;
            switch ($accion) {
                case ('CREATE');
                    $registros->mensaje = "Generó " . $objeto . " " . $info;
                    break;
                case ('UPDATE'):
                    $registros->mensaje = "Modificó " . $objeto . " " . $info;
                    break;
                case ('DELETE'):
                    $registros->mensaje = "Eliminó " . $objeto . " " . $info;
                    break;
                case ('ACTIVATE'):
                    $registros->mensaje = "Activó " . $objeto . " " . $info;
                    break;
            }
            $registros->save();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

}
