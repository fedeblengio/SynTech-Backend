<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Request;
use LdapRecord\Connection;


use App\Models\bedelias;
use App\Models\usuarios;

use LdapRecord\Models\ActiveDirectory\Group;
use LdapRecord\Models\ActiveDirectory\User;

class LoginTest extends TestCase
{
    
    // public function test_login()
    // {
    //     $credentials = $this->createNewUser();
    
    //     $response = $this->post('api/login',$credentials);

    //     $response->assertStatus(200);
    //     $response->assertJsonStructure([
    //         'connection',
    //         'datos',
    //     ]);
    //     $response->assertJson([
    //         'connection' => 'Success',
    //     ]);
    // }

    // public function test_error_login()
    // {
    //     $credentials = $this->createNewUser();
    //     $credentials['password'] = "randomText";
    //     $response = $this->post('api/login',$credentials);

    //     $response->assertStatus(200);
    //     $response->assertJsonStructure([
    //         'error',
    //     ]);
        
    //     $response->assertJson([
    //         'error' => 'Unauthenticated',
    //     ]);
    // }

    // private function createNewUser(){
    //     $randomID = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
       
    //     $user = usuarios::factory()->create([
    //         'id' => $randomID,
    //         'ou' => 'Bedelias'
    //     ]);
    //     $bedelias = bedelias::factory()->create([
    //         'id' => $randomID,
    //         'Cedula_Bedelia' =>$randomID,
    //         'cargo' => 'administrador'
    //     ]);
    //     $this->crearUsuarioLDAP($randomID);

    //     return ['username' => $randomID, 'password' => $randomID];
    // }

    // private function crearUsuarioLDAP($cedula)
    // {
    //     $user = (new User)->inside('ou=UsuarioSistema,dc=syntech,dc=intra');
    //     $user->cn =$cedula;
    //     $user->unicodePwd = $cedula;
    //     $user->samaccountname = $cedula;
    //     $user->save();
    //     $user->refresh();
    //     $user->userAccountControl = 66048;
    //     $user->save();
    // }
 
 } 
