<?php

namespace Tests\Feature;

use App\Models\token;
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

    use RefreshDatabase;
   
    
    public function testLogin()
    {
        $credentials = $this->createNewUser();
    
        $response = $this->post('api/login',$credentials);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'connection',
            'datos',
        ]);
        $response->assertJson([
            'connection' => 'Success',
        ]);
    }

    private function createNewUser(){
        $padded_number = str_pad(mt_rand(1, 9999999), 1 - strlen('1'), '0', STR_PAD_LEFT);
        $randomID = "1". $padded_number;
       
        $user = usuarios::factory()->create([
            'id' => $randomID,
            'ou' => 'Bedelias'
        ]);
        $bedelias = bedelias::factory()->create([
            'id' => $randomID,
            'Cedula_Bedelia' =>$randomID,
            'cargo' => 'administrador'
        ]);
        $this->crearUsuarioLDAP($randomID);

        return ['username' => $randomID, 'password' => $randomID];
    }

    private function crearUsuarioLDAP($cedula)
    {

        $this->deleteAllUsersInOU();

        $user = (new User)->inside('ou=Testing,dc=syntech,dc=intra');
        $user->cn =$cedula;
        $user->unicodePwd = $cedula;
        $user->samaccountname = $cedula;
        $user->save();
        $user->refresh();
        $user->userAccountControl = 66048;
        $user->save();
    }

  

    public function deleteAllUsersInOU()
    {
        $users = User::in('ou=Testing,dc=syntech,dc=intra')->get();
        foreach ($users as $user) {
            $user->delete();
        }
    }

    public function testErrorLogin()
    {
        $response = $this->post('api/login',[],[]);
        $response->assertStatus(302);
     
    }

    public function testLogout(){
        $token = token::factory()->create();
        $response = $this->post('api/logout',[],[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);   
    }

  
    
 
 } 
