<?php

namespace Tests\Feature;
use App\Models\materia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MateriaTest extends TestCase
{
   
    /**
     * A basic feature test example.
     *
     * @return void
     */

   
    public function test_listar_materias()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/materias');  
        
        $response->assertStatus(200);
    }
    

     public function test_agregar_materia()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['nombreMateria' => 'Obsidiana'];


        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/materia', $data);
        
        $response->assertStatus(200);
    }

    public function test_listar_una_materia()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Obsidiana')->first();
        $response = $this->withHeaders([
            'token' => $token,
        ])->getJson('/api/materia',['idMateria' => $materia->id]);  
        
        $response->assertStatus(200);
    }



    public function test_agregar_materia_existente()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['nombreMateria' => 'Obsidiana'];


        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/materia', $data);
        
        $response->assertStatus(416);
    } 

    public function test_modificar_materia()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Obsidiana')->first();
        $data = ['idMateria' => $materia->id, 'nuevoNombre' => 'Netherite']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->putJson('/api/materia', $data);
        
        $response->assertStatus(200);
    } 

    public function test_eliminar_materia()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Netherite')->first();
        $data = ['idMateria' => $materia->id]; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/materia', $data);
        
        $response->assertStatus(200);
    }
   

  
}
