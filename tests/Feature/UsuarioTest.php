<?php

namespace Tests\Feature;

/* use LdapRecord\Models\ActiveDirectory\User; */

use App\Models\usuarios;
use App\Models\alumnos;
use App\Models\profesores;
use App\Models\bedelias;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UsuarioTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */


  /*   public function test_login()
    {

        $data = ['username' => '51717993','password'=>'1'];
        $response = $this->postJson('/api/login', $data);
        $response->assertStatus(200);
    }





    public function test_listar_usuarios()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/usuarios');  
        
        $response->assertStatus(200);
    }

    

   public function test_agregar_usuario_alumno()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['cn' => 'Stevee', 'samaccountname' => '87654322', 'ou' => 'Alumno' , 'userPrincipalName' => 'stevee@syntech.intra' , 'unicodePwd' => '1' ];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/usuario', $data);
        
        $response->assertStatus(200);
    } 


    public function test_listar_un_usuario()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['username' => '51717993'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->getJson('/api/usuario', $data);
        
        $response->assertStatus(200);
    } */

  /*   public function test_modificar_un_usuario()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['username' => '12345678', 'newPassword' => '2' ,'nuevoEmail' => 'esteve@gmail.com','nuevoNombre' => '51717993'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->putJson('/api/usuario', $data);
        
        $response->assertStatus(200);
    } */

    

/*     public function test_eliminar_materia()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        
        $data = ['username' => '12345678']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/usuario', $data);
        
        $response->assertStatus(200);
    }  */

    
}
