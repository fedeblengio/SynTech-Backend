<?php

namespace Tests\Feature;
use App\Models\grupos;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GrupoTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /* public function test_listar_grupos()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/grupos');  
        
        $response->assertStatus(200);
    }
    

     public function test_agregar_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC','nombreCompleto'=>'Abduscan Matutino'];


        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/grupo', $data);
        
        $response->assertStatus(200);
    }

    public function test_listar_un_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->getJson('/api/grupo', $data);  
        
        $response->assertStatus(200);
    }

   



    public function test_agregar_grupo_existente()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC','nombreCompleto'=>'Abduscan Matutino'];


        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/grupo', $data);
        
        $response->assertStatus(416);
    } 

    public function test_modificar_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC', 'nuevoGrupo' => 'CBA' ,'nuevoNombreCompleto' =>'Tecnicatura Abduscan Nocturno' ]; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->putJson('/api/grupo', $data);
        
        $response->assertStatus(200);
    } 

    public function test_eliminar_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        
        $data = ['idGrupo' => 'CBA']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/grupo', $data);
        
        $response->assertStatus(200);
    } */
}
