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

    use RefreshDatabase;
    public function testRequestSinToken()
    {
        $response = $this->get('api/materia/');
        $response->assertStatus(401);
    }

    public function testCanShowMateria()
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

    public function testCanListAllMaterias()
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

    public function testCrearMateria()
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
        $this->assertDatabaseHas('materias', [
            'nombre' => $nombre,
        ]);
        $this->assertEquals($nombre, $response['nombre']);

    }

    public function testErrorCrearMateria()
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
        $this->assertDatabaseHas('materias', [
            'nombre' => $materia->nombre,
        ]);
        $response->assertStatus(400);
    }

    public function testUpdateMateria()
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
        $this->assertDatabaseHas('materias', [
            'nombre' => $nombre,
        ]);
        $this->assertEquals($nombre, $response['nombre']);
    }

    public function testErrorUpdateMateria()
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
        $this->assertDatabaseMissing('materias', [
            'nombre' => $nombre,
        ]);
    }
    public function testErrorUpdateRequestMateria()
    {
        $token = token::factory()->create();
        $materia = materia::factory()->create();
        $nombre = Str::random(10);
        $response = $this->put('api/materia/' . $materia->id, [], [
            'token' => [
                $token->token
            ]
        ]);
        $this->assertDatabaseMissing('materias', [
            'nombre' => $nombre,
        ]);
        $response->assertStatus(302);
    }

    public function testEliminarMateria()
    {
        $token = token::factory()->create();
        $materia = materia::factory()->create();
        $nombre = Str::random(10);
        $response = $this->delete('api/materia/' . $materia->id, [], [
            'token' => [
                $token->token
            ]
        ]);
        $this->assertDatabaseMissing('materias', [
            'nombre' => $nombre,
        ]);
        $response->assertStatus(200);
    }

    public function testErrorEliminarMateria()
    {
        $token = token::factory()->create();

        $nombre = Str::random(10);
        $response = $this->delete('api/materia/' . "00000", [], [
            'token' => [
                $token->token
            ]
        ]);
        $response->assertStatus(404);
    }


}