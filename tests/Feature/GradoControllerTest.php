<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\grupos;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\token;
use App\Models\materia;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GradoControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */

    use RefreshDatabase;
    public function testRequestSinToken()
    {
        $grado = Grado::factory()->create();
        $response = $this->get('api/grado/' . $grado->id);
        $response->assertStatus(401);
    }
    public function testCanShowGrado()
    {
        $token = token::factory()->create();
        $grado = Grado::factory()->create();
        $response = $this->get('api/grado/' . $grado->id, [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee($grado->id);
        $response->assertSee($grado->grado);
        $response->assertSee($grado->carrera_id);
    }


    public function testCanCreateGrado()
    {
        $token = token::factory()->create();
        $grados = ['1er Grado', '2do Grado'];
        $response = $this->post('api/carrera', [
            'nombre' => Str::random(10),
            "plan" => Carbon::now()->format('Y'),
            "categoria" => "Informatica",
            "grados" => $grados
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(201);
        $this->assertEquals($response['grado'][0]['grado'], $grados[0]);
        $this->assertEquals($response['grado'][1]['grado'], $grados[1]);
    }
    public function testUpdateGrado()
    {
        $token = token::factory()->create();
        $grado = Grado::factory()->create();

        $response = $this->put('api/grado/' . $grado->id, [
            "grado" => "Updated grado",
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200);
        $response->assertSee("Updated grado");
        $this->assertDatabaseHas('grados', [
            'id' => $grado->id,
            'grado' => "Updated grado"
        ]);
    }

    public function testErrorUpdateGrado(){
        $token = token::factory()->create();
 
        $response = $this->put('api/grado/' . "432876", [
            "grado" => "Updated grado2",
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(404);
        $this->assertDatabaseMissing('grados', [
            'id' => "432876",
            'grado' => "Updated grado2"
        ]);
    
    }

    public function testAgregarMateriaGrado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();

        $materia = materia::factory()->create();

        $materiaAgregar = [
            'materia_id' => $materia->id,
            'cantidad_horas' =>"20"
        ];
        $response = $this->post('api/grado/' .$grado->id."/materia",  $materiaAgregar, [
                'token' => [
                    $token->token
                ]
            ]);

    
        $response->assertStatus(200);
        $this->assertDatabaseHas('carrera_tiene_materias', [
            'grado_id' => $grado->id,
            'materia_id' => $materia->id,
            'cantidad_horas' =>"20"
        ]);
        $this->assertEquals($response['materias'][0]['id'],$materia->id);
    }

    public function testErrorAgregarMateriaGrado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();

        $materiaAgregar = [
            'cantidad_horas' =>"20"
        ];
        $response = $this->post('api/grado/' .$grado->id."/materia",  $materiaAgregar, [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(302);
        $this->assertDatabaseMissing('carrera_tiene_materias', [
            'grado_id' => $grado->id,
            'cantidad_horas' =>"20"
        ]);
    }


    public function testEliminarMateriaGrado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();
        $materia = materia::factory()->create();
        $grado->materias()->attach($materia->id,['cantidad_horas'=>"20", 'carrera_id' => $grado->carrera_id]);
     

        $response = $this->delete('api/grado/' .$grado->id."/materia/".$materia->id, [], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('carrera_tiene_materias', [
            'grado_id' => $grado->id,
            'materia_id' => $materia->id,
            'cantidad_horas' =>"20"
        ]);
        $this->assertEquals($grado->materia,null);
    }

    public function testErrorEliminarMateriaGrado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();
        $materia = materia::factory()->create();
      
     
        $response = $this->delete('api/grado/' ."randomText"."/materia/".$materia->id, [], [
                'token' => [
                    $token->token
                ]
            ]);
        $this->assertDatabaseMissing('carrera_tiene_materias', [
            'grado_id' => $grado->id,
            'materia_id' => $materia->id,
            'cantidad_horas' =>"20"
        ]);
        $response->assertStatus(404);
    }

    public function testEliminarGrado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();

        $response = $this->delete('api/carrera/' .$grado->carrera->id."/grado/".$grado->id, [], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200);
        $grado = Grado::find($grado->id);
        $this->assertEquals($grado,null);
    }

    public function testErrorEliminarGrado(){
        $token = token::factory()->create();
        $random = rand(1000,2000);

        $response = $this->delete('api/carrera/' .$random."/grado/".$random, [], [
                'token' => [
                    $token->token
                ]
            ]);
       
        $response->assertStatus(404);
        
    }

}