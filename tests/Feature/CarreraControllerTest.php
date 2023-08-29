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

     use RefreshDatabase;


    public function testRequestSinToken()
    {
        $response = $this->get('api/carrera/');
        $response->assertStatus(401);
    }
    public function testCanShowCarrera()
    {

        $token = token::factory()->create();
        $carrera = Carrera::factory()->create();
    
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

    public function testCanListAllCarreras()
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

    public function testCanCreateCarrera()
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
        $this->assertDatabaseHas('carreras', [
            'nombre' => $response['nombre'],
            'plan' => $response['plan'],
            'categoria' => $response['categoria']
        ]);
    }

    public function testCanNotCreateCarrera()
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
        $response->assertStatus(302);

    }


    public function testUpdateCarrera()
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
        $this->assertDatabaseHas('carreras', [
            'id' => $carrera->id,
            'categoria' => 'Informatica y Redes'
        ]);
    }

    public function testErrorUpdateCarreraNoExiste()
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
        $response->assertStatus(404);
        $this->assertDatabaseMissing('carreras', [
            'id' => "9090989",
            'categoria' => 'Informatica y Redes'
        ]);
    }

    public function testErrorUpdateCarreraNombreNull()
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
        $response->assertStatus(302);
        $this->assertDatabaseMissing('carreras', [
            'id' => "9090989",
            'categoria' => 'Informatica y Redes'
        ]);
    }

    public function testDeleteCarrera(){

        $token = token::factory()->create();
        $carrera = Carrera::factory()->create();
        $response = $this->delete('api/carrera/' .$carrera->id,[],[
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200);
        $carrera = Carrera::find($carrera->id);
        $this->assertEquals(null, $carrera);
    }

    public function testErrorDeleteCarrera(){

        $token = token::factory()->create();
        $carrera = ['id' =>  Str::random(10)];
    
        $response = $this->delete('api/carrera/' .$carrera['id'],[],[
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(400);  
    }





}