<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\bedelias;
use App\Models\usuarios;
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

    public function test_list_users_bedelia()
    {
        $token = token::factory()->create();
        $bedelia1 =  $this->createNewBedelia();
        $bedelia2 =  $this->createNewBedelia();
        $response = $this->get('api/bedelia',[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($bedelia1);
        $response->assertSee($bedelia2);

    }

    public function test_show_user_bedelia(){
        $token = token::factory()->create();
      
        $bedelia =  $this->createNewBedelia();
      
        $response = $this->get('api/bedelia/'.$bedelia,[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($bedelia);

    }

    public function test_error_show_user_bedelia(){
        $token = token::factory()->create();
      
        $response = $this->get('api/bedelia/'."testUser",[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(404);

    }
    public function createNewBedelia(){

        $randomID = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
       
        $user = usuarios::factory()->create([
            'id' => $randomID,
            'ou' => 'Bedelias'
        ]);
        $bedelias = bedelias::factory()->create([
            'id' => $randomID,
            'Cedula_Bedelia' =>$randomID,
            'cargo' => 'administrador'
        ]);

        return $randomID;
    }

    public function test_update_user_bedelia(){
        $userID = $this->createNewBedelia();
        $token = token::factory()->create();
        $updatedUser = [
            'nombre' => 'Jane',
            'apellido' => 'Doe',
            'email' => 'jane.doe@example.com',
            'genero' => 'Femenino',
        ];
    
        $response = $this->put("api/bedelia/".$userID, $updatedUser, [
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

 

 


}
