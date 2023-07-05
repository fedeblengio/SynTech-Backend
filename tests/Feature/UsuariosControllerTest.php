<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\token;
use App\Models\usuarios;
use App\Models\bedelias;
use LdapRecord\Models\ActiveDirectory\User;


class UsuariosControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testCambiarContrasenia(){
        $token = token::factory()->create();
        $user = $this->createNewUser();
        $newPassword = '123456';
        
        $response = $this->put('api/usuario/'.$user['username'].'/contrasenia',[
            'contrasenia' => $newPassword,
        ],[
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $credentials = [
            'username' => $user['username'],
            'password' => $newPassword
        ];
        $response2 = $this->post('api/login',$credentials);
        $response2->assertStatus(200);
        $response2->assertJsonStructure([
            'connection',
            'datos',
        ]);


        $this->deleteUserInOU($user['username']);

    

    }

    private function createNewUser(){
        $randomID = str_pad(mt_rand(10000000, 99999999), 7);
       
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
        $user = (new User)->inside('ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->cn =$cedula;
        $user->unicodePwd = $cedula;
        $user->samaccountname = $cedula;
        $user->save();
        $user->refresh();
        $user->userAccountControl = 66048;
        $user->save();
    }

    public function deleteUserInOU($id)
    {
        $user = User::find('cn=' . $id . ',ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->delete();
    }
}
