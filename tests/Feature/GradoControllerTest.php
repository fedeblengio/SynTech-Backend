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
    public function test_request_sin_token()
    {
        $grado = Grado::factory()->create();
        $response = $this->get('api/grado/' . $grado->id);
        $response->assertStatus(401);
    }
    public function test_can_show_grado()
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


    public function test_can_create_grado()
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
    public function test_update_grado()
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
    }

    public function test_error_update_grado(){
        $token = token::factory()->create();
 
        $response = $this->put('api/grado/' . "432876", [
            "grado" => "Updated grado",
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(404);
    
    }

    public function test_agregar_materia_grado(){
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
        $this->assertEquals($response['materias'][0]['id'],$materia->id);
    }

    public function test_error_agregar_materia_grado(){
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
    }


    public function test_eliminar_materia_grado(){
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

        $this->assertEquals($grado->materia,null);
    }

    public function test_error_eliminar_materia_grado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();
        $materia = materia::factory()->create();
      
     
        $response = $this->delete('api/grado/' ."randomText"."/materia/".$materia->id, [], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(404);
    }

    public function test_eliminar_grado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();

        $response = $this->delete('api/carrera/' .$grado->carrera->id."/grado/".$grado->id, [], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200);
    }

    public function test_error_eliminar_grado(){
        $token = token::factory()->create();
        $grado = Grado::factory()->create();
        $materia = materia::factory()->create();
        $grado->materias()->attach($materia->id,['cantidad_horas'=>"20", 'carrera_id' => $grado->carrera_id]);
     

        $response = $this->delete('api/carrera/' .$grado->carrera->id."/grado/".$grado->id, [], [
                'token' => [
                    $token->token
                ]
            ]);
       
        $response->assertStatus(403);
    }

}