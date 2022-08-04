<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Storage;
use App\Models\usuarios;
use App\Models\alumnos;
use App\Models\profesores;
use App\Models\bedelias;
use App\Models\alumnos_pertenecen_grupos;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use LdapRecord\Models\ActiveDirectory\User;
use App\Http\Controllers\agregarUsuarioGrupoController;
use App\Http\Controllers\profesorDictaMateriaController;

class usuariosController extends Controller
{

    public function index(Request $request)
    {
        $cargo = json_decode(base64_decode($request->header('token')))->cargo;

        if ($cargo == "Adscripto" || $cargo == "Administrativo") {  
            return response()->json(
                DB::table('usuarios')
                    ->select('*')
                    ->where('ou','!=', "Bedelias")
                    ->get()
            );
        }

        return response()->json(usuarios::all());
    }

    public function create(Request $request)
    {
        $usuarioAD = User::find('cn=' . $request->samaccountname . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        /* $usuarioDB = usuarios::where('id', $request->samaccountname)->first(); */
        $usuarioDB = DB::table('usuarios')
            ->select('*')
            ->where('id', $request->samaccountname)
            ->first();
        if ($usuarioDB) {
            if ($usuarioDB->deleted_at) {
                self::activarUsuarioAD($usuarioAD);
                self::activarUsuarioDB($request);
                return response()->json(['status' => 'Success'], 200);
            }

            return response()->json(['error' => 'Forbidden'], 403);
        } else {
            try {
                self::agregarUsuarioDB($request);
                self::agregarUsuarioAD($request);

                switch ($request->ou) {
                    case "Bedelias":
                        self::agregarUsuarioBedelias($request);
                        break;
                    case "Alumno":
                        self::agregarUsuarioAlumno($request);
                        break;
                    case "Profesor":
                        self::agregarUsuarioProfesor($request);
                        break;
                }

                return response()->json(['status' => 'Success'], 200);
            } catch (\Throwable $th) {
                return response()->json(['status' => 'Error'], 400);
                return $th;
            }
        }

        if ($usuarioAD) {
            return response()->json(['error' => 'Forbidden'], 403);
            $this->exit();
        }
    }

    public function agregarUsuarioDB($request)
    {
        $usuarioDB = new usuarios;
        $usuarioDB->id = $request->samaccountname;
        $usuarioDB->nombre = $request->name . " " . $request->surname;
        $usuarioDB->email = $request->userPrincipalName;
        $usuarioDB->ou = $request->ou;
        $usuarioDB->save();
    }
    public function agregarUsuarioAlumno($request)
    {
        $alumno = DB::table('alumnos')
            ->select('*')
            ->where('id', $request->samaccountname)
            ->first();
        if ($alumno) {
            if ($alumno->deleted_at) {
                DB::table('alumnos')
                    ->where('id', $request->samaccountname)
                    ->update(['deleted_at' => null]);
            }
        } else {
            $alumno = new alumnos;
            $alumno->Cedula_Alumno = $request->samaccountname;
            $alumno->id = $request->samaccountname;
            $alumno->save();
        }
        if ($request->idGrupos) {
            foreach ($request->idGrupos as $idG) {

                agregarUsuarioGrupoController::store(new Request([
                    "idAlumno" => $request->samaccountname,
                    "idGrupo" =>  $idG,
                ]));
            }
        }
    }

    public function agregarUsuarioProfesor($request)
    {
        $profesores = DB::table('profesores')
            ->select('*')
            ->where('id', $request->samaccountname)
            ->first();
        if ($profesores) {
            if ($profesores->deleted_at) {
                DB::table('profesores')
                    ->where('id', $request->samaccountname)
                    ->update(['deleted_at' => null]);
            }
        } else {
            $profesores = new profesores;
            $profesores->Cedula_Profesor = $request->samaccountname;
            $profesores->id = $request->samaccountname;
            $profesores->save();
        }
        if ($request->idMaterias) {
            foreach ($request->idMaterias as $m) {
                profesorDictaMateriaController::store($m, $request->samaccountname);
            }
        }
    }

    public function agregarUsuarioBedelias($request)
    {
        $bedelias = DB::table('bedelias')
            ->select('*')
            ->where('id', $request->samaccountname)
            ->first();
        if ($bedelias) {
            if ($bedelias->deleted_at) {
                DB::table('bedelias')
                    ->where('id', $request->samaccountname)
                    ->update(['deleted_at' => null, 'cargo' => $request->cargo]);
            }
        } else {
            $bedelias = new bedelias;
            $bedelias->Cedula_Bedelia = $request->samaccountname;
            $bedelias->id = $request->samaccountname;
            $bedelias->cargo = $request->cargo;
            $bedelias->save();
        }
    }

    public function agregarUsuarioAD(Request $request)
    {

        $user = (new User)->inside('ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->cn = $request->samaccountname;
        $user->unicodePwd =  $request->samaccountname;
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

    public function activarUsuarioAD($usuarioAD)
    {
        $usuarioAD->userAccountControl = 66048;
        $usuarioAD->save();
        $usuarioAD->refresh();
    }

    public function activarUsuarioDB($request)
    {
        $usuarioDB = [
            "nombre" => $request->cn,
            "email" => $request->userPrincipalName,
            "ou" => $request->ou,
            "deleted_at" => null
        ];
        DB::table('usuarios')
            ->where('id', $request->samaccountname)
            ->update($usuarioDB);

        switch ($request->ou) {
            case "Bedelias":
                self::agregarUsuarioBedelias($request);
                break;
            case "Alumno":
                self::agregarUsuarioAlumno($request);
                break;
            case "Profesor":
                self::agregarUsuarioProfesor($request);
                break;
        }
    }


    public function show(request $request)
    {
        $userDB = usuarios::where('id', $request->username)->first();
        $userDB->imagen_perfil = base64_encode(Storage::disk('ftp')->get($userDB->imagen_perfil));
        return response()->json($userDB);
    }


    public function update(Request $request)
    {
        if ($request->newPassword) {
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
        } else {
            self::update_db($request);
        }
    }

    public function update_db($request)
    {
        $usuarios = usuarios::where('id', $request->username)->first();
        if ($usuarios) {
            DB::update('UPDATE usuarios SET nombre="' . $request->nuevoNombre . '" ,  email="' . $request->nuevoEmail . '" WHERE id="' . $request->username . '";');
        }
    }


    public function destroy(request $request)
    {
        $existe = usuarios::where('id', $request->id)->first();

        $user = User::find('cn=' . $request->id . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        try {
            if ($existe) {
                $existe->delete();
                $user->userAccountControl = 514;
                $user->save();
                $user->refresh();

                self::eliminarPersona($existe);
                /* DB::delete('delete from usuarios where username="' . $request->username . '" ;'); */

                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }


    public function eliminarPersona($existe)
    {


        switch ($existe->ou) {
            case "Bedelias":
                $bedelias = bedelias::where('id', $existe->id)->first();
                $bedelias->delete();
                break;
            case "Alumno":
                $alumnos = alumnos::where('id', $existe->id)->first();
                $alumnos->delete();
                self::eliminarAlumnoGrupo($existe);
                break;
            case "Profesor":
                $profesores = profesores::where('id', $existe->id)->first();
                $profesores->delete();
                self::eliminarMateriaProfesor($existe);
                self::eliminarMateriaGrupo($existe);
                break;
        }
    }

    public function eliminarAlumnoGrupo($existe)
    {
        DB::table('alumnos_pertenecen_grupos')
            ->where('idAlumnos', $existe->id)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
    }

    public function eliminarMateriaProfesor($existe)
    {
        DB::table('profesor_dicta_materia')
            ->where('idProfesor', $existe->id)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
    }
    public function eliminarMateriaGrupo($existe)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idProfesor', $existe->id)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
    }
}
