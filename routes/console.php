<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

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

    $user = User::create(["id"=>"33667835","nombre"=>"Admin","email"=>"akusterpiriz@gmail.com","ou"=>"Bedelias","imagen_perfil"=>"default_picture.png"]);
    $bedelias = Bedelias::create(["id"=>"33667835","Cedula_Bedelia"=>"33667835","cargo"=>"administrador"]);

    $this->comment("Firt User created sucessfully");
});