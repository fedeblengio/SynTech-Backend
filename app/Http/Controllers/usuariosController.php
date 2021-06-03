<?php

namespace App\Http\Controllers;

use App\Models\usuarios;
use App\Models\alumnos;
use App\Models\profesores;
use App\Models\bedelias;

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

            try {
                self::agregarUsuarioDB($request);
                self::agregarUsuarioAD($request);

                switch ($request->ou) {
                    case "Alumno":
                        self::agregarUsuarioAlumno($request);
                        break;
                    case "Profesor":
                        self::agregarUsuarioProfesor($request);
                        break;
                    case "Bedelias":
                        self::agregarUsuarioBedelias($request);
                        break;
                }




                return response()->json(['status' => 'Success'], 200);
            } catch (\Throwable $th) {
                return response()->json(['status' => 'Error'], 400);
            }


        }
    }

    public function agregarUsuarioDB($request){
        $usuarioDB = new usuarios;
        $usuarioDB->username = $request->samaccountname;
        $usuarioDB->nombre = $request->cn;
        $usuarioDB->email = $request->userPrincipalName;
        $usuarioDB->ou = $request->ou;
        $usuarioDB->save();
    }
    public function agregarUsuarioAlumno($request){
        $alumno = new alumnos;
        $alumno->Cedula_Alumno = $request->samaccountname;
        $alumno->idAlumnos = $request->samaccountname;
        $alumno->save();

    }

    public function agregarUsuarioProfesor($request){
        $profesores = new profesores;
        $profesores->Cedula_Profesor = $request->samaccountname;
        $profesores->idProfesor = $request->samaccountname;
        $profesores->grado="7";
        $profesores->save();
    }

    public function agregarUsuarioBedelias($request){
        $bedelias = new bedelias;
        $bedelias->Cedula_Bedelia = $request->samaccountname;
        $bedelias->idBedelia = $request->samaccountname;
        $profesores->cargo="7";
        $bedelias->save();

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
