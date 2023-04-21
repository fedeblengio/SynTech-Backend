<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use LdapRecord\Models\ActiveDirectory\User;
use App\Models\token;


class BedeliasControllerTest extends TestCase
{

    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_user_bedelia()
    {
        $token = token::factory()->create();
        $randomID = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $newUser = [
            'samaccountname' => $randomID,
            'name' => "John",
            'surname' => "Doe",
            'userPrincipalName' => 'jdoe@example.com',
            'ou' => 'Bedelias',
            'cargo' => 'Supervisor',
        ];

        $response = $this->post('api/usuario', $newUser,[
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee($newUser['userPrincipalName']);
        $response->assertSee($newUser['ou']);
        
        $this->deleteCreatedLDAPUser($newUser['samaccountname']);
    }

    public function deleteCreatedLDAPUser($samaccountname)
    {
        $user = User::find('cn='.$samaccountname.',ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->delete();
    }
}
