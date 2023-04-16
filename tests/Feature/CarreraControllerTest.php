<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\grupos;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\token;
use App\Models\Carrera;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CarreraControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */


    public function test_request_sin_token()
    {
        $response = $this->get('api/carrera/');
        $response->assertStatus(401);
    }
    public function test_can_show_carrera()
    {

        $token = token::factory()->create();
        // Create a Carrera using the factory
        $carrera = Carrera::factory()->create();

        // Send a GET request to the show method of the CarreraController
        $response = $this->get('api/carrera/' . $carrera->id, [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee($carrera->nombre);
        $response->assertSee($carrera->plan);
        $response->assertSee($carrera->categoria);
    }

    public function test_can_list_all_carreras()
    {
        $token = token::factory()->create();
        $carrera1 = Carrera::factory()->create();
        $carrera2 = Carrera::factory()->create();

        $response = $this->get('api/carrera', [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($carrera1->nombre);
        $response->assertSee($carrera2->nombre);
        $response->assertSee($carrera1->plan);
        $response->assertSee($carrera2->plan);
        $response->assertSee($carrera1->categoria);
        $response->assertSee($carrera2->categoria);
    }

    public function test_can_create_carrera()
    {
        $token = token::factory()->create();

        $response = $this->post('api/carrera', [
            'nombre' => Str::random(10),
            "plan" => Carbon::now()->format('Y'),
            "categoria" => "Informatica"
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(201);
    }

    public function test_can_not_create_carrera()
    {
        $token = token::factory()->create();
        $carrera = Carrera::factory()->create();
        $response = $this->post('api/carrera', [
            'nombre' => $carrera->nombre,
            "plan" => $carrera->plan,
            "categoria" => $carrera->categoria
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(302); // No pasa request validate porque la carrera existe 
    }


    public function test_update_carrera()
    {
        $token = token::factory()->create();
        $carrera = Carrera::factory()->create();
        $response = $this->put('api/carrera/' . $carrera->id, [
            'nombre' => $carrera->nombre,
            "plan" => $carrera->plan,
            "categoria" => "Informatica y Redes"
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(200);
        $response->assertSee($carrera->id);
        $this->assertEquals("Informatica y Redes", $response['categoria']);
    }

    public function test_error_update_carrera_no_existe()
    {
        $token = token::factory()->create();
        $carrera = Carrera::factory()->create();
        $response = $this->put('api/carrera/' ."9090989", [
            'nombre' => $carrera->nombre,
            "plan" => $carrera->plan,
            "categoria" => "Informatica y Redes"
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(404); // Carrera no existe
    }

    public function test_error_update_carrera_nombre_null()
    {
        $token = token::factory()->create();
        $carrera = Carrera::factory()->create();
        $response = $this->put('api/carrera/' ."9090989", [
            "plan" => $carrera->plan,
            "categoria" => "Informatica y Redes"
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(302); // Nombre es un field required
    }

    public function test_delete_carrera(){

        $token = token::factory()->create();
        $carrera = Carrera::factory()->create();
        $response = $this->delete('api/carrera/' .$carrera->id,[],[
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200); 
    }

    public function test_error_delete_carrera(){

        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $carrera = $grupo->grado->carrera;
    
        $response = $this->delete('api/carrera/' .$carrera->id,[],[
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(409);  
    }





}