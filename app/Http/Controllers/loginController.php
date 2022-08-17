<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Models\token;
use App\Models\usuarios;
use Illuminate\Http\Request;
use LdapRecord\Models\ActiveDirectory\User;
use Illuminate\Support\Str;
use LdapRecord\Connection;
use Carbon\Carbon;
use App\Models\Registros;
use Illuminate\Support\Facades\Storage;

class loginController extends Controller
{

    public function index()
    {
        $allUsers =  User::all();
        return response()->json($allUsers);
    }

    public function connect(Request $request)
    {

        $connection = new Connection([
            'hosts' => ['192.168.50.139'],
        ]);

        $datos = self::traerDatos($request);

        $connection->connect();


        if ($connection->auth()->attempt($request->username . '@syntech.intra', $request->password, $stayBound = true)) {
            $registros = new registros;
            $registros->idUsuario = $request->username;
            $registros->app = "BACKOFFICE";
            $registros->accion = "LOGIN";
            $registros->mensaje = "Inicio sesion ";
            $registros->save();
            return [
                'connection' => 'Success',
                'datos' => $datos,
            ];
        } else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        }
    }

    public function traerDatos($request)
    {

            $u= DB::table('usuarios')
            ->select('usuarios.id','usuarios.nombre','usuarios.email','usuarios.ou','bedelias.cargo','usuarios.genero','usuarios.imagen_perfil')
            ->join('bedelias', 'bedelias.id', '=', 'usuarios.id')
            ->where('usuarios.id', $request->username)
            ->whereNull('usuarios.deleted_at')
            ->first();

        $datos = [
            "username" => $u->id,
            "nombre" => $u->nombre,
            "cargo" => $u->cargo,
            "ou" => $u->ou,
            "email" => $u->email,
            "genero" => $u->genero,
            "imagen_perfil" => $u->imagen_perfil,
        ];

        $base64data = base64_encode(json_encode($datos));
        $tExist = token::where('token', $base64data)->first();


        if ($tExist) {
            $tExist->delete();
            self::guardarToken($base64data);
        } else {
            self::guardarToken($base64data);
        }

        return  $base64data;
    }




    public function guardarToken($token)
    {
        $t = new token;
        $t->token = $token;
        $t->fecha_vencimiento = Carbon::now()->addMinutes(90);
        $t->save();
    }

    public function traerArchivos(Request $request)
    {
        return base64_encode(Storage::disk('ftp')->get($request->nombre_archivo));
    }
    public function traerImagenPerfil(Request $request )
    { 
        $usuarioDB = usuarios::where('id', $request->id)->first();

        
        $base64imagen = base64_encode(Storage::disk('ftp')->get($usuarioDB->imagen_perfil));
    
        return response()->json($base64imagen);
    }
}
