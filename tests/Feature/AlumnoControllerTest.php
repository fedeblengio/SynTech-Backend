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
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;

    public function test_create_user_alumno()
    {
        $token = token::factory()->create();
        $padded_number = str_pad(mt_rand(1, 9999999), 1 - strlen('1'), '0', STR_PAD_LEFT);
        $randomID = "1". $padded_number;
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


    public function test_list_users_alumno()
    {
        $token = token::factory()->create();
        $alumno1 =  $this->createNewAlumno();
   
        $response = $this->get('api/alumno',[
            'token' => [
                $token->token,
            ],
        ]);
       
        $response->assertStatus(200);
    
        $this->assertEquals($response[0]['id'], $alumno1);
      
    }

    public function test_show_user_alumno(){
        $token = token::factory()->create();
      
        $alumno =  $this->createNewAlumno();
      
        $response = $this->get('api/alumno/'.$alumno,[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($alumno);

    }

    public function test_error_show_user_alumno(){
        $token = token::factory()->create();
      
        $response = $this->get('api/alumno/'."testUser",[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);

    }
    public function createNewAlumno(){

        $padded_number = str_pad(mt_rand(1, 9999999), 1 - strlen('1'), '0', STR_PAD_LEFT);
        $randomID = "1". $padded_number;
       
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

    public function test_update_user_alumno(){
        $userID = $this->createNewAlumno();
        $token = token::factory()->create();
        $updatedUser = [
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'email' => 'jane.doe@example.com',
            'genero' => 'Femenino',
        ];
    
        $response = $this->put("api/alumno/".$userID, $updatedUser, [
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

    public function test_error_update_user_alumno(){
        $userID = "RandomUser";
        $token = token::factory()->create();
        $updatedUser = [
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'email' => '2314214',
            'genero' => 'Femenino',
        ];
    
        $response = $this->put("api/alumno/".$userID, $updatedUser, [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);
    }

    //  Route::get('/alumno/{id}/grupos', 'App\Http\Controllers\AlumnoController@gruposNoPertenecenAlumno');

    public function test_list_grupos_no_pertenece_alumno(){
        $token = token::factory()->create();
        $alumno =  $this->createNewAlumno();
        $grupo = grupos::factory()->create();
        $response = $this->get('api/alumno/'.$alumno.'/grupos',[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $this->assertEquals($response[0]['id'], $grupo->id);
    }

    public function test_error_list_grupos_no_pertenece_alumno(){
        $token = token::factory()->create();
        $alumno = "randomId";
        $grupo = grupos::factory()->create();
        $response = $this->get('api/alumno/'.$alumno.'/grupos',[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);
    }
}
