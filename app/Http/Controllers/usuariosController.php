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
use App\Http\Controllers\RegistrosController;
use App\Http\Controllers\profesorDictaMateriaController;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;


class usuariosController extends Controller
{

    public function index(Request $request)
    {
        $cargo = json_decode(base64_decode($request->header('token')))->cargo;

        if ($cargo == "Adscripto" || $cargo == "Administrativo") {
            return self::getAllButNotBedelias();
        } elseif ($cargo == "Director" || $cargo == "Subdirector") {

            return self::getAllButNotSuperUser();
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
            /* try { */
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
            $details = [
                'usuario' => $request->samaccountname,
                'contrasenia' => $request->samaccountname
            ];

            Mail::to($request->userPrincipalName)->send(new \App\Mail\MyTestMail($details));
           
            return response()->json(['status' => 'Success'], 200);
            /* } catch (\Throwable $th) {
                return response()->json(['status' => 'Error'], 400);
                return $th;
            } */
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
                self::activarAlumno($request);
            }
        } else {
            self::agregarAlumno($request);
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
                self::activarProfesor($request);
            }
        } else {
            self::agregarProfesor($request);
        }
        if ($request->idMaterias) {
            foreach ($request->idMaterias as $m) {
                profesorDictaMateriaController::store($m, $request->samaccountname, $request->header('token'));
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
                self::activarBedelia($request);
            }
        } else {
            self::agregarBedelia($request);
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
            "nombre" => $request->name . " " . $request->surname,
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

    public function getFullHistory()
    {
        return DB::table('historial_registros')
            ->select('historial_registros.id', 'historial_registros.idUsuario', 'usuarios.nombre', 'historial_registros.app', 'historial_registros.accion', 'historial_registros.mensaje', 'historial_registros.created_at')
            ->join('usuarios', 'usuarios.id', '=', 'historial_registros.idUsuario')
            ->orderByDesc('created_at')
            ->get();
    }

    public function show(request $request)
    {
        $userDB = usuarios::where('id', $request->username)->first();
        $userDB->imagen_perfil = base64_encode(Storage::disk('ftp')->get($userDB->imagen_perfil));

        $infoUser = self::returnMoreInfoUser($userDB);
        return response()->json(['user' => $userDB, 'info' => $infoUser]);
    }

    public function returnMoreInfoUser($userOBJ)
    {
        if ($userOBJ->ou == 'Bedelias')
            return DB::table('bedelias')->select('*')->where('id', $userOBJ->id)->first();
        if ($userOBJ->ou == 'Profesor')
            return self::getMoreInfoProfesor($userOBJ);
        if ($userOBJ->ou == 'Alumno')
            return self::getMoreInfoAlumno($userOBJ);
    }

    public function cambiarFotoUsuario(Request $request)
    {
        $usuarioDB = DB::table('usuarios')
            ->select('*')
            ->where('id', $request->id)
            ->first();


        if ($request->hasFile("archivo")) {
            $file = $request->archivo;

            $nombre = time() . "_" . $file->getClientOriginalName();
            Storage::disk('ftp')->put($nombre, fopen($request->archivo, 'r+'));

            DB::table('usuarios')
                ->where('id', $request->id)
                ->update(['imagen_perfil' => $nombre]);

            return response()->json(['status' => 'Success'], 200);
        }

        if ($usuarioDB->imagen_perfil != "default_picture.png") {
            Storage::disk('ftp')->delete($usuarioDB->imagen_perfil);

            DB::table('usuarios')
                ->where('id', $request->id)
                ->update(['imagen_perfil' => "default_picture.png"]);

            return response()->json(['status' => 'Success'], 200);
        }
        return response()->json(['status' => 'Success'], 200);
    }

    public function cambiarContrasenia(Request $request)
    {
        $user = User::find('cn=' . $request->id . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        if ($request->contrasenia) {
            $user->unicodePwd = $request->contrasenia;
        } else {
            $user->unicodePwd = $request->id;
        }
        $user->save();
        $user->refresh();
        RegistrosController::store("CONTRASEÑA", $request->header('token'), "UPDATE", $request->id);
        return response()->json(['status' => 'Success'], 200);
    }

    public function update(Request $request)
    {
        try {
            $usuario = usuarios::where('id', $request->idUsuario)->first();
            if ($usuario) {
                $usuario->nombre = $request->nombre;
                $usuario->email = $request->email;
                $usuario->genero = $request->genero;
                $usuario->save();
            }
            RegistrosController::store("USUARIO", $request->header('token'), "UPDATE", $request->idUsuario);
            return response()->json(['status' => 'Success'], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
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

                self::eliminarPersona($existe, $request->header('token'));
                /* DB::delete('delete from usuarios where username="' . $request->username . '" ;'); */
                RegistrosController::store("USUARIO", $request->header('token'), "DELETE", $request->id);
                return response()->json(['status' => 'Success'], 200);
            }
            return response()->json(['status' => 'Bad Request'], 400);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }


    public function eliminarPersona($existe, $token)
    {


        switch ($existe->ou) {
            case "Bedelias":
                $bedelias = bedelias::where('id', $existe->id)->first();
                $bedelias->delete();
                RegistrosController::store("BEDELIAS", $token, "DELETE", $existe->id);
                break;
            case "Alumno":
                $alumnos = alumnos::where('id', $existe->id)->first();
                $alumnos->delete();
                self::eliminarAlumnoGrupo($existe, $token);
                RegistrosController::store("ALUMNO", $token, "DELETE", $existe->id);
                break;
            case "Profesor":
                $profesores = profesores::where('id', $existe->id)->first();
                $profesores->delete();
                self::eliminarMateriaProfesor($existe, $token);
                self::eliminarMateriaGrupo($existe, $token);
                RegistrosController::store("PROFESOR", $token, "DELETE", $existe->id);
                break;
        }
    }

    public function eliminarAlumnoGrupo($existe, $token)
    {
        DB::table('alumnos_pertenecen_grupos')
            ->where('idAlumnos', $existe->id)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("ALUMNO GRUPO", $token, "DELETE", $existe->id);
    }

    public function eliminarMateriaProfesor($existe, $token)
    {
        DB::table('profesor_dicta_materia')
            ->where('idProfesor', $existe->id)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("MATERIA PROFESOR", $token, "DELETE", $existe->id);
    }
    public function eliminarMateriaGrupo($existe, $token)
    {
        DB::table('grupos_tienen_profesor')
            ->where('idProfesor', $existe->id)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("GRUPO PROFESOR", $token, "DELETE", $existe->id);
    }

    /**
     * @param $request
     * @return void
     */
    public function activarAlumno($request): void
    {
        DB::table('alumnos')
            ->where('id', $request->samaccountname)
            ->update(['deleted_at' => null]);
        RegistrosController::store("ALUMNO", $request->header('token'), "ACTIVATE", $request->samaccountname);
    }

    /**
     * @param $request
     * @return void
     */
    public function agregarAlumno($request): void
    {
        $alumno = new alumnos;
        $alumno->Cedula_Alumno = $request->samaccountname;
        $alumno->id = $request->samaccountname;
        $alumno->save();
        RegistrosController::store("ALUMNO", $request->header('token'), "CREATE", $request->samaccountname);
    }

    /**
     * @param $request
     * @return void
     */
    public function activarProfesor($request): void
    {
        DB::table('profesores')
            ->where('id', $request->samaccountname)
            ->update(['deleted_at' => null]);
        RegistrosController::store("PROFESOR", $request->header('token'), "ACTIVATE", $request->samaccountname);
    }

    /**
     * @param $request
     * @return void
     */
    public function agregarProfesor($request): void
    {
        $profesores = new profesores;
        $profesores->Cedula_Profesor = $request->samaccountname;
        $profesores->id = $request->samaccountname;
        $profesores->save();
        RegistrosController::store("PROFESOR", $request->header('token'), "CREATE", $request->samaccountname);
    }

    /**
     * @param $request
     * @return void
     */
    public function activarBedelia($request): void
    {
        DB::table('bedelias')
            ->where('id', $request->samaccountname)
            ->update(['deleted_at' => null, 'cargo' => $request->cargo]);
        RegistrosController::store("BEDELIAS", $request->header('token'), "ACTIVATE", $request->samaccountname . " - " . $request->cargo);
    }

    /**
     * @param $request
     * @return void
     */
    public function agregarBedelia($request): void
    {
            $bedelias = new bedelias;
            $bedelias->Cedula_Bedelia = $request->samaccountname;
            $bedelias->id = $request->samaccountname;
            $bedelias->cargo = $request->cargo? $request->cargo : "Adscripto";
            $bedelias->save();
            RegistrosController::store("BEDELIAS", $request->header('token'), "CREATE", $request->samaccountname . " - " . $request->cargo);      

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllButNotBedelias(): \Illuminate\Http\JsonResponse
    {
        return response()->json(
            DB::table('usuarios')
                ->select('*')
                ->where('ou', '!=', "Bedelias")
                ->where('deleted_at', NULL)
                ->get()
        );
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAllButNotSuperUser(): \Illuminate\Http\JsonResponse
    {
        $second = DB::table('usuarios')
            ->select('*')
            ->leftJoin('bedelias', 'usuarios.id', '=', 'bedelias.id')
            ->where('bedelias.cargo', '!=', "administrador")
            ->where('usuarios.deleted_at', NULL)
            ->get();

        $final = DB::table('usuarios')
            ->select('*')
            ->where('ou', '!=', "Bedelias")
            ->where('deleted_at', NULL)
            ->get();

        return response()->json($second->merge($final));
    }

    /**
     * @param $userOBJ
     * @return \Illuminate\Support\Collection
     */
    public function getMoreInfoProfesor($userOBJ): \Illuminate\Support\Collection
    {
        return DB::table('profesor_dicta_materia')
            ->select('materias.nombre', 'materias.id')
            ->join('materias', 'profesor_dicta_materia.idMateria', '=', 'materias.id')
            ->where('profesor_dicta_materia.idProfesor', $userOBJ->id)
            ->where('profesor_dicta_materia.deleted_at', NULL)
            ->get();
    }

    /**
     * @param $userOBJ
     * @return \Illuminate\Support\Collection
     */
    public function getMoreInfoAlumno($userOBJ): \Illuminate\Support\Collection
    {
        return DB::table('alumnos_pertenecen_grupos')
            ->select('grupos.idGrupo')
            ->join('grupos', 'alumnos_pertenecen_grupos.idGrupo', '=', 'grupos.idGrupo')
            ->where('alumnos_pertenecen_grupos.idAlumnos', $userOBJ->id)
            ->where('alumnos_pertenecen_grupos.deleted_at', NULL)
            ->get();
    }
}
