<?php

namespace Tests\Feature;

use App\Models\materia;
use App\Models\token;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\profesores;
use App\Models\usuarios;
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
        $padded_number = str_pad(mt_rand(1, 9999999), 1 - strlen('1'), '0', STR_PAD_LEFT);
        $randomID = "1". $padded_number;
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

    public function test_list_materia_profesor_no_tiene(){
        $token = token::factory()->create();
        $profesor =  $this->createNewProfesor();
        $materia = materia::factory()->create();
        $response = $this->get('api/profesor/'.$profesor.'/materias',[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($materia->id);

    }

   
    public function test_list_users_profesor()
    {
        $token = token::factory()->create();
        $profesor1 =  $this->createNewProfesor();
        $profesor2 =  $this->createNewProfesor();
        $response = $this->get('api/profesor',[
            'token' => [
                $token->token,
            ],
        ]);
       
        $response->assertStatus(200);
        $response->assertSee($profesor1);
        $response->assertSee($profesor2);

    }

    public function test_show_user_profesor(){
        $token = token::factory()->create();
      
        $profesor =  $this->createNewProfesor();
      
        $response = $this->get('api/profesor/'.$profesor,[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($profesor);

    }

    public function test_error_show_user_profesor(){
        $token = token::factory()->create();
      
        $response = $this->get('api/profesor/'."testUser",[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);

    }
    public function createNewProfesor(){

        $padded_number = str_pad(mt_rand(1, 9999999), 1 - strlen('1'), '0', STR_PAD_LEFT);
        $randomID = "1". $padded_number;
       
        $user = usuarios::factory()->create([
            'id' => $randomID,
            'ou' => 'Profesor'
        ]);
        $profesor = profesores::factory()->create([
            'id' => $randomID,
            'Cedula_Profesor' => $randomID,
        ]);

        return $randomID;
    }

    public function test_update_user_profesor(){
        $userID = $this->createNewProfesor();
        $token = token::factory()->create();
        $updatedUser = [
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'email' => 'jane.doe@example.com',
            'genero' => 'Femenino',
        ];
    
        $response = $this->put("api/profesor/".$userID, $updatedUser, [
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

    public function test_error_update_user_profesor(){
        $userID = "RandomUser";
        $token = token::factory()->create();
        $updatedUser = [
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'email' => '2314214',
            'genero' => 'Femenino',
        ];
    
        $response = $this->put("api/profesor/".$userID, $updatedUser, [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);
    }

}