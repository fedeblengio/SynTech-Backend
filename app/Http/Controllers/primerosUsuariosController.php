<?php

namespace App\Http\Controllers;

use App\Http\Controllers\usuariosController;
use Illuminate\Http\Request;

class primerosUsuariosController extends usuariosController
{
    public static function crearPrimerUsuario(Request $request)
    {


        $usuario1 = new Request([
            "samaccountname" => "33667835",
            "name" => 'Admin',
            "surname" => '1',
            "userPrincipalName" => 'akusterpiriz@gmail.com',
            "ou" => "Bedelias",
            "cargo" => "administrador",
        ]);
        $usuario2 = new Request([
            "samaccountname" => "32282024",
            "name" => 'Admin',
            "surname" => '2',
            "userPrincipalName" => 'aaronnoguera07@gmail.com',
            "ou" => "Bedelias",
            "cargo" => "administrador",
        ]);
        if ($request->token === "am9qbyBubyBraW15b3UgbmEgYm91a2VuIGV5ZXMgb2YgaGVhdmVu") {
            try {
                usuariosController::create($usuario1);
                usuariosController::create($usuario2);
                return response()->json(['status' => 'Success.'], 200);
            } catch (\Throwable $th) {
                return response()->json(['status' => 'Error.'], 401);
            }
        }
    }
}
