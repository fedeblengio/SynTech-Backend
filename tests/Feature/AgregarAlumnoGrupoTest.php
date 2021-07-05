<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AgregarAlumnoGrupoTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    /* public function test_listar_alumnos_sin_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/alumnos');  
        
        $response->assertStatus(200);
    }

    public function test_agregar_alumno_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'TB2' , 'idAlumnos' => '51717993'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/alumno', $data);  
        
        $response->assertStatus(200);
    } 

    public function test_eliminar_materia()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
       
        $data = ['idAlumnos' => '51717993']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/alumno', $data);
        
        $response->assertStatus(200);
    } */

}
