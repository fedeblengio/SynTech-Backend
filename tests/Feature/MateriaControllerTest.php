<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\token;
use App\Models\materia;
use Tests\TestCase;

class MateriaControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_request_sin_token()
    {
        $response = $this->get('api/materia/');
        $response->assertStatus(401);
    }

    public function test_can_show_materia()
    {
        $token = token::factory()->create();
        $materia = materia::factory()->create();
        $response = $this->get('api/materia/' . $materia->id, [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee($materia->id);
        $response->assertSee($materia->nombre);
    }

    public function test_can_list_all_materias()
    {
        $token = token::factory()->create();
        $materia1 = materia::factory()->create();
        $materia2 = materia::factory()->create();

        $response = $this->get('api/materia', [
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);

        $response->assertSee($materia1->id);
        $response->assertSee($materia1->nombre);
        $response->assertSee($materia2->id);
        $response->assertSee($materia2->nombre);
    }

    public function test_crear_materia()
    {
        $token = token::factory()->create();
        $nombre = Str::random(10);
        $response = $this->post('api/materia', [
            'nombre' => $nombre,
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(201);
        $this->assertEquals($nombre, $response['nombre']);

    }

    public function test_error_crear_materia()
    {
        $token = token::factory()->create();
        $materia = materia::factory()->create();

        $response = $this->post('api/materia', [
            'nombre' => $materia->nombre,
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(400);
    }

    public function test_update_materia()
    {
        $token = token::factory()->create();
        $materia = materia::factory()->create();
        $nombre = Str::random(10);
        $response = $this->put('api/materia/' . $materia->id, [
            'nombre' => $nombre,
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200);
        $this->assertEquals($nombre, $response['nombre']);
    }

    public function test_error_update_materia()
    {
        $token = token::factory()->create();

        $nombre = Str::random(10);
        $response = $this->put('api/materia/' . "00000", [
            'nombre' => $nombre,
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(400);
    }
    public function test_error_update_request_materia()
    {
        $token = token::factory()->create();
        $materia = materia::factory()->create();
        $nombre = Str::random(10);
        $response = $this->put('api/materia/' .$materia->id, [], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(302);
    }

    public function test_eliminar_materia()
    {
        $token = token::factory()->create();
        $materia = materia::factory()->create();
        $nombre = Str::random(10);
        $response = $this->delete('api/materia/' .$materia->id, [], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200);
    }

    public function test_error_eliminar_materia()
    {
        $token = token::factory()->create();
       
        $nombre = Str::random(10);
        $response = $this->delete('api/materia/' ."00000", [], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(404);
    }


}