<?php

namespace Tests\Feature;

use App\Models\token;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use LdapRecord\Models\ActiveDirectory\User;
class ProfesorControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    use RefreshDatabase;
    public function test_create_user_profesor()
    {
        $token = token::factory()->create();
        $randomID = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        $newTeacher = [
            'samaccountname' =>$randomID,
            'name' => "George",
            'surname' => "Lucas",
            'userPrincipalName' => 'jlucas@example.com',
            'ou' => "Profesor",
            'materias' => [],
        ];

        $response = $this->post('api/usuario', $newTeacher,[
            'token' => [
                $token->token,
            ],
        ]);
        $this->deleteCreatedLDAPUser($newTeacher['samaccountname']);
        $response->assertStatus(200);
        $response->assertSee($newTeacher['userPrincipalName']);
        $response->assertSee($newTeacher['ou']);

        $this->assertTrue(true);
    }

    public function deleteCreatedLDAPUser($samaccountname)
    {
        $user = User::find('cn='.$samaccountname.',ou=UsuarioSistema,dc=syntech,dc=intra');
        $user->delete();
    }
}