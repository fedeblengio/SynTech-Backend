<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;



/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Artisan::command('create:firstUsers',function (){

    $usuario = new Request([
        "samaccountname" => "32282214",
        "name" => 'Admin',
        "surname" => '2',
        "userPrincipalName" => 'akusterpiriz@gmail.com',
        "ou" => "Bedelias",
        "cargo" => "administrador",
    ]);

    usuariosController::create($usuario);
    // $user = usuarios::create(["id"=>"33667830","nombre"=>"Admin","email"=>"akusterpiriz@gmail.com","ou"=>"Bedelias","imagen_perfil"=>"default_picture.png"]);
    // $bedelias = Bedelias::create(["id"=>"33667830","Cedula_Bedelia"=>"33667830","cargo"=>"administrador"]);

    // $user = (new User)->inside('ou=UsuarioSistema,dc=syntech,dc=intra');
    
    // $user->cn = $user->id;
    // $user->unicodePwd = $user->id;
    // $user->samaccountname = $user->id;

   
    // $user->userAccountControl = 66048;
    // $user->save();
    // $user->refresh();

    $this->comment("Firt User created sucessfully");
});