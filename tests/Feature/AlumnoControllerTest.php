<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\token;
use LdapRecord\Models\ActiveDirectory\User;

class AlumnoControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_create_user_alumno()
    {
        $token = token::factory()->create();
        $randomID = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $newStudent = [
            'samaccountname' =>$randomID,
            'name' => "Thomas",
            'surname' => "Edison",
            'userPrincipalName' => 'tedison@example.com',
            'ou' => "Alumno",
            'grupos' => [],
        ];

        $response = $this->post('api/usuario', $newStudent,[
            'token' => [
                $token->token,
            ],
        ]);
        $this->deleteCreatedLDAPUser($newStudent['samaccountname']);
        $response->assertStatus(200);
        $response->assertSee($newStudent['userPrincipalName']);
        $response->assertSee($newStudent['ou']);
    
    }
    public function deleteCreatedLDAPUser($samaccountname)
    {
        $user = User::find('cn='.$samaccountname.',ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->delete();
    }
}
