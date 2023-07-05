<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Storage;
use App\Models\usuarios;
use App\Models\alumnos;
use App\Models\profesores;
use App\Models\bedelias;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Traits\verificarUsuarioPerteneceGrupoAD;
use LdapRecord\Models\ActiveDirectory\User;
use App\Http\Controllers\RegistrosController;
use LdapRecord\Models\ActiveDirectory\Group;
use Illuminate\Support\Facades\Mail;
use App\Mail\MyTestMail;
use Illuminate\Support\Facades\App;


class usuariosController extends Controller
{
    const FILE_HEADERS = [
        "samaccountname" => 0,
        "name" => 1,
        "surname" => 2,
        "userPrincipalName" => 3,
        "cargo" => 4,
    ];
    use verificarUsuarioPerteneceGrupoAD;

    public function index(Request $request)
    {

        $id = json_decode(base64_decode($request->header('token')))->username;
        $user = User::find('cn=' . $id . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        $notBedelias = [
            'Administrativo',
            'Adscripto',
        ];
        $notSuperUser = [
            'Director',
            'Subdirector',
        ];
        $adminRol = ['Supervisor'];
        if ($this->verificarPerteneceGrupoAD($user, $notBedelias)) {
            return self::getAllButNotBedelias();
        }
        if ($this->verificarPerteneceGrupoAD($user, $notSuperUser)) {
            return self::getAllButNotSuperUser();
        }
        if ($this->verificarPerteneceGrupoAD($user, $adminRol)) {
            return response()->json(usuarios::all());
        }
        return response()->json(['Error' => "Unauthorized", 403]);
    }

    public function store(Request $request)
    {

        $request->validate([
            'samaccountname' => 'required|string|max:8|min:8|unique:usuarios,id',
            'name' => 'required|string|max:80',
            'surname' => 'required|string|max:80',
            'userPrincipalName' => 'required|email',
            'ou' => 'required|string',
        ]);

        try {
            self::agregarUsuarioAD($request);
            $usuarioDB = self::agregarUsuarioDB($request);
            $usuarioDB['id'] = $request->samaccountname;

            switch ($request->ou) {
                case "Bedelias":
                    self::agregarBedelia($request);
                    break;
                case "Alumno":
                    self::agregarAlumno($request);
                    break;
                case "Profesor":
                    self::agregarProfesor($request);
                    break;
            }
            return response()->json($usuarioDB);
        } catch (\ValueError $e) {
            return response()->json(['status' => 'Error', 'message' => 'Bad request'], 400);
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

        return $usuarioDB;
    }


    public function agregarUsuarioAD(Request $request)
    {
        $user = new User;
        if (App::environment(['testing'])) {
            $user->inside('ou=Testing,dc=syntech,dc=intra');
        } else {
            $user->inside('ou=UsuarioSistema,dc=syntech,dc=intra');
        }
        $user->cn = $request->samaccountname;
        $user->unicodePwd = $request->samaccountname;
        $user->samaccountname = $request->samaccountname;
        try {
            $user->save();
            $user->refresh();
            $user->userAccountControl = 66048;
            $user->save();
        } catch (\LdapRecord\LdapRecordException $e) {
            return response()->json(['status' => 'Error', 'message' => 'Bad request'], 400);
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

    public function show($id)
    {
        $userDB = usuarios::where('id', $id)->first();
        if (empty($userDB)) {
            return;
        }
        if (isset($userDB->imagen_perfil)) {
            $userDB->imagen_perfil = base64_encode(Storage::disk('ftp')->get($userDB->imagen_perfil));
        }
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

    public function traerImagen($id)
    {
        $usuario = usuarios::find($id);
        $base64imagen = base64_encode(Storage::disk('ftp')->get($usuario->imagen_perfil));
        return response()->json($base64imagen);
    }

    public function cambiarImagen(Request $request, $id)
    {
        $usuario = usuarios::find($id);

        if (empty($usuario)) {
            return response()->json(['status' => 'Error', 'message' => 'Bad request'], 400);
        }

        if ($request->hasFile('archivo')) {
            return $this->cambiarImagenDePerfil($request, $usuario);
        }
        if ($usuario->imagen_perfil != "default_picture.png") {
            return $this->establecerImagenPorDefecto($request, $usuario);
        }

        if ($usuario->imagen_perfil == "default_picture.png") {
            return response()->json(['status' => 'Success'], 200);
        }

        return response()->json(['error' => 'Forbidden'], 403);
    }

    public function cambiarImagenDePerfil(Request $request, $usuario)
    {
        $file = $request->archivo;
        $nombre = time() . "_" . $file->getClientOriginalName();
        Storage::disk('ftp')->put($nombre, fopen($request->archivo, 'r+'));

        $usuario->imagen_perfil = $nombre;
        $usuario->save();

        return response()->json(['status' => 'Success'], 200);
    }


    public function establecerImagenPorDefecto(Request $request, $usuario)
    {
        Storage::disk('ftp')->delete($usuario->imagen_perfil);
        $usuario->imagen_perfil = "default_picture.png";
        $usuario->save();
        return response()->json(['status' => 'Success'], 200);
    }

    public function cambiarContrasenia(Request $request, $id)
    {
        $request->validate([
            'contrasenia' => 'string | nullable',
        ]);

        $user = User::find('cn=' . $id . ',ou=UsuarioSistema,dc=syntech,dc=intra');

        if ($request->contrasenia) {
            $user->unicodePwd = $request->contrasenia;
        } else {
            $user->unicodePwd = $id;
        }
        $user->save();
        $user->refresh();
        RegistrosController::store("CONTRASEÑA", $request->header('token'), "UPDATE", $id);
        return response()->json(['status' => 'Success'], 200);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nombre' => 'required|string|max:80',
            'email' => 'required|email',
            'genero' => 'string | nullable',
        ]);
        try {
            $usuario = usuarios::where('id', $id)->first();
            if ($usuario) {
                $usuario->nombre = $request->nombre . " " . $request->apellido;
                $usuario->email = $request->email;
                $usuario->genero = $request->genero;
                $usuario->save();
            }
            RegistrosController::store("USUARIO", $request->header('token'), "UPDATE", $request->idUsuario);
            return response()->json([
                'usuario' => $usuario,
                'status' => 'Success'
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['status' => 'Bad Request'], 400);
        }
    }

    public function activarUsuario($id)
    {

        $u = usuarios::withTrashed()->where('id', $id)->first();
        if (empty($u)) {
            return response()->json(['status' => 'Bad Request'], 400);
        }

        $user = User::find('cn=' . $id . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->userAccountControl = 66048;
        $user->save();
        $user->refresh();
        $usuario = usuarios::onlyTrashed()->find($id);
        $usuario->restore();

        switch ($usuario->ou) {
            case "Bedelias":
                bedelias::onlyTrashed()->find($id)->restore();
                break;
            case "Alumno":
                alumnos::onlyTrashed()->find($id)->restore();
                break;
            case "Profesor":
                profesores::onlyTrashed()->find($id)->restore();
                break;
        }

        return response()->json(['status' => 'Success'], 200);
    }


    public function destroy(Request $request, $id)
    {
        $existe = usuarios::where('id', $id)->first();

        if (empty($existe)) {
            return response()->json(['status' => 'Bad Request'], 400);
        }

        $user = User::find('cn=' . $id . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        try {
            $existe->delete();
            $user->userAccountControl = 514;
            $user->save();
            $user->refresh();
            self::eliminarPersona($existe, $request->header('token'));
            RegistrosController::store("USUARIO", $request->header('token'), "DELETE", $request->id);
            return response()->json(['status' => 'Success'], 200);

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

                RegistrosController::store("ALUMNO", $token, "DELETE", $existe->id);
                break;
            case "Profesor":
                $profesores = profesores::where('id', $existe->id)->first();
                $profesores->delete();
                self::eliminarMateriaProfesor($existe, $token);

                RegistrosController::store("PROFESOR", $token, "DELETE", $existe->id);
                break;
        }
    }


    public function eliminarMateriaProfesor($existe, $token)
    {
        DB::table('profesor_dicta_materia')
            ->where('idProfesor', $existe->id)
            ->update(['deleted_at' => Carbon::now()->addMinutes(23)]);
        RegistrosController::store("MATERIA PROFESOR", $token, "DELETE", $existe->id);
    }



    public function agregarAlumno($request): void
    {
        $alumno = new alumnos;
        $alumno->id = $request->samaccountname;
        $alumno->Cedula_Alumno = $request->samaccountname;
        $alumno->id = $request->samaccountname;
        $alumno->save();

        if (!empty($request->grupos)) {
            $alumno->asignarGrupos($request->grupos, $alumno->Cedula_Alumno);
        }

        self::agregarUsuarioGrupoAD($alumno->Cedula_Alumno, "Alumno");
        RegistrosController::store("ALUMNO", $request->header('token'), "CREATE", $request->samaccountname);
    }


    public function agregarProfesor($request): void
    {
        $profesores = new profesores;
        $profesores->Cedula_Profesor = $request->samaccountname;
        $profesores->id = $request->samaccountname;
        $profesores->save();

        $profesores->asignarMaterias($request->materias, $profesores->Cedula_Profesor);

        self::agregarUsuarioGrupoAD($profesores->Cedula_Profesor, "Profesor");

        RegistrosController::store("PROFESOR", $request->header('token'), "CREATE", $request->samaccountname);
    }


    public function agregarBedelia($request): void
    {
        $bedelias = new bedelias;
        $bedelias->Cedula_Bedelia = $request->samaccountname;
        $bedelias->id = $request->samaccountname;
        $bedelias->cargo = $request->cargo ? $request->cargo : "Adscripto";
        $bedelias->save();


        self::agregarUsuarioGrupoAD($bedelias->Cedula_Bedelia, $bedelias->cargo);

        RegistrosController::store("BEDELIAS", $request->header('token'), "CREATE", $request->samaccountname . " - " . $request->cargo);

    }

    public function agregarUsuarioGrupoAD($idUsuario, $grupo)
    {

        $group = Group::find('cn=' . $grupo . ',ou=Grupos,dc=syntech,dc=intra');

        if (App::environment(['testing'])){
            $user = User::find('cn=' . $idUsuario . ',ou=Testing,dc=syntech,dc=intra');
        }
        else{
            $user = User::find('cn=' . $idUsuario . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        }

        $group->members()->attach($user);

    }

    public function eliminarUsuarioGrupoAD($idUsuario, $grupo)
    {

        $group = Group::find('cn=' . $grupo . ',ou=Grupos,dc=syntech,dc=intra');

        $user = $group->members()->where('cn', '=', $idUsuario)->first();

        $group->members()->detach($user);

    }

    public function getAllButNotBedelias()
    {
        return response()->json(
            DB::table('usuarios')
                ->select('*')
                ->where('ou', '!=', "Bedelias")
                ->where('deleted_at', NULL)
                ->get()
        );
    }


    public function getAllButNotSuperUser()
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


    public function getMoreInfoProfesor($userOBJ): \Illuminate\Support\Collection
    {
        return DB::table('profesor_dicta_materia')
            ->select('materias.nombre', 'materias.id')
            ->join('materias', 'profesor_dicta_materia.idMateria', '=', 'materias.id')
            ->where('profesor_dicta_materia.idProfesor', $userOBJ->id)
            ->where('profesor_dicta_materia.deleted_at', NULL)
            ->get();
    }


    public function getMoreInfoAlumno($userOBJ): \Illuminate\Support\Collection
    {
        return DB::table('alumnos_pertenecen_grupos')
            ->select('grupos.idGrupo')
            ->join('grupos', 'alumnos_pertenecen_grupos.idGrupo', '=', 'grupos.idGrupo')
            ->where('alumnos_pertenecen_grupos.idAlumnos', $userOBJ->id)
            ->where('alumnos_pertenecen_grupos.deleted_at', NULL)
            ->get();
    }

    public function importFromCSV($file, $type)
    {
        try {
            $data = \Excel::toArray([], $file)[0];
            unset($data[0]);
            foreach ($data as $value) {
                $user = $this->buildRequestDataForUser($value, $type);
                $this->store($user);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }

        return response()->json(['message' => 'CSV file imported successfully']);
    }
    private function buildRequestDataForUser($value, $type)
    {
        $request = new Request();
        $request->merge([
            'samaccountname' => (string) $value[self::FILE_HEADERS['samaccountname']],
            'name' => $value[self::FILE_HEADERS['name']],
            'surname' => $value[self::FILE_HEADERS['surname']],
            'userPrincipalName' => $value[self::FILE_HEADERS['userPrincipalName']],
            'ou' => $type,
            'cargo' => $value[self::FILE_HEADERS['cargo']] ?? "",
        ]);

        return $request;
    }


}