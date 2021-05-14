<?php

namespace App\Http\Controllers;
use App\Models\token;
use App\Models\usuarios;
use Illuminate\Http\Request;
use LdapRecord\Models\ActiveDirectory\User;
use Illuminate\Support\Str;
use LdapRecord\Connection;
use Carbon\Carbon;


class loginController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
  

    public function index()
    {
        $allUsers =  User::all();
        return response()->json($allUsers);
    }

    public function connect(Request $request)
    {
        $token = Str::random(60);
        $connection = new Connection([
            'hosts' => ['192.168.1.73'],
        ]);

        $datos = self::traerDatos($request); 

        $connection-> connect();

        if ($connection->auth()->attempt($request->username.'@syntech.intra', $request->password, $stayBound = true)) {
            return [
                'connection' => 'Success',
                 'datos' => $datos, 
                 ];
        }else {
            return response()->json(['error' => 'Unauthenticated.'], 401);
        } 

    }

    public function traerDatos($request){
        $u = usuarios::where('username', $request->username)->first();
        $t = new token;
        
        $datos=[
            "username" => $u->username,
            "nombre" => $u->nombre,
            "ou" => $u->ou
        ];
        $base64data= base64_encode(json_encode($datos));

        $t->token=$base64data;
        $t->fecha_vencimiento=Carbon::now()->addMinutes(60);
        $t->save();
        
        return  $base64data;
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function show(usuarios $usuarios)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function edit(usuarios $usuarios)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, usuarios $usuarios)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\usuarios  $usuarios
     * @return \Illuminate\Http\Response
     */
    public function destroy(usuarios $usuarios)
    {
        //
    }
}
