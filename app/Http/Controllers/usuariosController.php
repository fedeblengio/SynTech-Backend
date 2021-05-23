<?php

namespace App\Http\Controllers;

use App\Models\usuarios;
use Illuminate\Http\Request;
use LdapRecord\Models\ActiveDirectory\User;

class usuariosController extends Controller
{
   
    public function index()
    {
        return response()->json(usuarios::all());
    }

    public function create(Request $request)
    {
        $usuarioAD = User::find('cn=' . $request->cn . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        $usuarioDB = usuarios::where('username', $request->samaccountname)->first();



        if ($usuarioAD) {
            return response()->json(['error' => 'Forbidden'], 403);
            $this->exit();
        }
        if ($usuarioDB) {
            return response()->json(['error' => 'Forbidden'], 403);
        } else {
            $usuarioDB = new usuarios;
            $usuarioDB->username = $request->samaccountname;
            $usuarioDB->nombre = $request->cn;
            $usuarioDB->email = $request->userPrincipalName;
            $usuarioDB->ou = $request->ou;
            $usuarioDB->save();

            self::agregarUsuarioAD($request);
            return response()->json(['status' => 'Success'], 200);
        }
    }

    public function agregarUsuarioAD(Request $request)
    {

        $user = (new User)->inside('ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->cn = $request->samaccountname;
        $user->unicodePwd = $request->unicodePwd;
        $user->samaccountname = $request->samaccountname;

        $user->save();
        $user->refresh();
        $user->userAccountControl = 66048;

        try {
            $user->save();
        } catch (\LdapRecord\LdapRecordException $e) {
            return "Fallo al crear usuario " . $e;
        }
    }


    public function show(request $request)
    {
        $userDB = usuarios::where('username', $request->username)->first();
        return response()->json($userDB);
    }


    public function update(Request $request, usuarios $usuarios)
    {
        try {
            $user = User::find('cn=' . $request->username . ',ou=UsuarioSistema,dc=syntech,dc=intra');
            $user->unicodePwd = $request->newPassword;
            $user->save();
            $user->refresh();
            self::update_db($request);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function update_db($request)
    {
        $usuarios = usuarios::where('username', $request->username)->first();
        $usuarios->nombre = $request->nuevoNombre;
        $usuarios->email = $request->nuevoEmail;
        $usuarios->save();
    }

    public function destroy(request $request)
    {
        $user = User::find('cn=' . $request->username . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        try {
            $user->delete();
            $u = usuarios::where('username', $request->username)->first();
            $u->delete();
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }
}
