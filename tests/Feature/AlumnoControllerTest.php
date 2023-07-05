<?php

namespace Tests\Feature;

use App\Models\alumnos;
use App\Models\grupos;
use App\Models\usuarios;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\token;
use LdapRecord\Models\ActiveDirectory\User;

class AlumnoControllerTest extends TestCase
{


    use RefreshDatabase;

    public function testCreateUserAlumno()
    {
        $token = token::factory()->create();
        $randomID = str_pad(mt_rand(10000000, 99999999), 7);
        $newStudent = [
            'samaccountname' => $randomID,
            'name' => "Thomas",
            'surname' => "Edison",
            'userPrincipalName' => 'tedison@example.com',
            'ou' => "Alumno",
            'grupos' => [],
        ];

        $response = $this->post('api/usuario', $newStudent, [
            'token' => [
                $token->token,
            ],
        ]);
        /* $this->deleteCreatedLDAPUser($newStudent['samaccountname']); */
        $response->assertStatus(200);
        $response->assertSee($newStudent['userPrincipalName']);
        $response->assertSee($newStudent['ou']);

        $this->assertDatabaseHas('usuarios', [
            'id' => $newStudent['samaccountname'],
            'ou' => $newStudent['ou'],
        ]);

    }
    public function deleteCreatedLDAPUser($samaccountname)
    {
        $user = User::find('cn=' . $samaccountname . ',ou=Testing,dc=syntech,dc=intra');
        if (!empty($user)) {
            $user->delete();
        }
    }


    public function testListUsersAlumno()
    {
        $token = token::factory()->create();
        $alumno1 = $this->createNewAlumno();

        $response = $this->get('api/alumno', [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);

        $this->assertEquals($response[0]['id'], $alumno1);

    }

    public function testShowUserAlumno()
    {
        $token = token::factory()->create();

        $alumno = $this->createNewAlumno();

        $response = $this->get('api/alumno/' . $alumno, [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($alumno);

    }

    public function testErrorShowUserAlumno()
    {
        $token = token::factory()->create();

        $response = $this->get('api/alumno/' . "testUser", [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);

    }
    public function createNewAlumno()
    {

        $randomID = str_pad(mt_rand(10000000, 99999999), 7);

        $user = usuarios::factory()->create([
            'id' => $randomID,
            'ou' => 'Alumno'
        ]);
        $profesor = alumnos::factory()->create([
            'id' => $randomID,
            'Cedula_Alumno' => $randomID,
        ]);

        return $randomID;
    }

    public function testUpdateUserAlumno()
    {
        $userID = $this->createNewAlumno();
        $token = token::factory()->create();
        $updatedUser = [
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'email' => 'jane.doe@example.com',
            'genero' => 'Femenino',
        ];

        $response = $this->put("api/alumno/" . $userID, $updatedUser, [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'usuario' => [
                'nombre' => 'Jane Doe',
                'email' => 'jane.doe@example.com',
                'genero' => 'Femenino',
            ],
            'status' => 'Success',
        ]);

    }

    public function testErrorUpdateUserAlumno()
    {
        $userID = "RandomUser";
        $token = token::factory()->create();
        $updatedUser = [
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'email' => '2314214',
            'genero' => 'Femenino',
        ];

        $response = $this->put("api/alumno/" . $userID, $updatedUser, [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);
    }


    public function testListGruposNoPerteneceAlumno()
    {
        $token = token::factory()->create();
        $alumno = $this->createNewAlumno();
        $grupo = grupos::factory()->create();
        $response = $this->get('api/alumno/' . $alumno . '/grupos', [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $this->assertEquals($response[0]['id'], $grupo->id);
    }

    public function testErrorListGruposNoPerteneceAlumno()
    {
        $token = token::factory()->create();
        $alumno = "randomId";
        $grupo = grupos::factory()->create();
        $response = $this->get('api/alumno/' . $alumno . '/grupos', [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);
    }
}