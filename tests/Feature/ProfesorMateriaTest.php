<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProfesorMateriaTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
  /*  public function test_listar_materias_sin_profesor()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idProfesor' => ''];
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/profesor');  
        
        $response->assertStatus(200);
    }

    public function test_listar_profesores()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/profesores');  
        
        $response->assertStatus(200);
    }
    public function test_agregar_materia_profesor()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idMateria' => '' , 'idProfesor' => ''];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/profesor', $data);  
        
        $response->assertStatus(200);
    } 

    public function test_agregar_materia_profesor_igual()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idMateria' => '' , 'idProfesor' => ''];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/profesor', $data);  
        
        $response->assertStatus(406);
    } 


    public function test_eliminar_materia_profesor()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
       
        $data = ['idMateria' => '' , 'idProfesor' => '']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/profesor', $data);
        
        $response->assertStatus(200);
    } */
}
