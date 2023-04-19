<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\grupos;
use App\Models\materia;
use App\Models\token;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Database\Factories\GradoFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GrupoControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     *
     * @return void
     */

    //  Route::get('/grupo/{id}', 'App\Http\Controllers\gruposController@show');
    //  Route::post('/grupo', 'App\Http\Controllers\gruposController@store');
    //  Route::delete('/grupo/{id}', 'App\Http\Controllers\gruposController@destroy');
    //  Route::get('/grupo', 'App\Http\Controllers\gruposController@index');
    //  Route::get('/grupo/{id}/materias-libres', 'App\Http\Controllers\gruposController@listarMateriasSinProfesor');



    //  Route::put('/grupo/{id}', 'App\Http\Controllers\gruposController@update');



    //  Route::get('/grupo/{id}/alumnos', 'App\Http\Controllers\gruposController@alumnosNoPertenecenGrupo');
    //  Route::delete('/grupo/{id}/alumno/{idAlumno}', 'App\Http\Controllers\gruposController@eliminarAlumnoGrupo');
    //  Route::delete('/grupo/{id}/profesor/{idProfesor}', 'App\Http\Controllers\gruposController@eliminarProfesorGrupo');

    public function test_request_sin_token()
    {
        $response = $this->get('api/grupo/');
        $response->assertStatus(401);
    }
    public function test_can_mostrar_grupo()
    {
        $token = token::factory()->create();
        $grupo = grupos::factory()->create();

        $response = $this->get('api/grupo/' . $grupo->idGrupo, [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee($grupo->id);
        $response->assertSee($grupo->idGrupo);
    }
    public function test_error_mostrar_grupo_no_existente()
    {
        $token = token::factory()->create();

        $idGrupoNoExistente = Str::random(4);

        $response = $this->get('api/grupo/' . $idGrupoNoExistente, [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(404);
        $response->assertDontSee('id');
        $response->assertDontSee('idGrupo');
    }

    public function test_can_crear_grupo()
    {
        $token = token::factory()->create();
        $grado = Grado::Factory()->create();

        $response = $this->post('api/grupo', [
            'idGrupo' => Str::random(4),
            "anioElectivo" => Carbon::now()->format('Y'),
            "grado_id" => $grado->id,
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(201);
        $data = $response->json();
        $this->assertDatabaseHas('grupos', [
            'idGrupo' => $data['idGrupo'],
            'anioElectivo' => $data['anioElectivo'],
            'grado_id' => $data['grado_id'],
        ]);
    }
    public function test_error_crear_grupo()
    {
        $token = Token::factory()->create();
        $grado = Grado::Factory()->create();

        $response = $this->post('api/grupo', [
            'anioElectivo' => Carbon::now()->format('Y'),
            'grado_id' => $grado->id,
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(302);
    }

    public function test_can_eliminar_grupo()
    {
        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $response = $this->delete('api/grupo/' . $grupo->idGrupo, [], [
            'token' => [
                $token->token,
            ]
        ]);
        $response->assertStatus(200);
        $this->assertDatabaseMissing('grupos', [
            'id' => $grupo->idGrupo,
        ]);
    }
    public function test_error_eliminar_grupo_inexistente()
    {
        $token = token::factory()->create();
        $grupo_id = 123456123;
        $response = $this->delete('api/grupo/' . $grupo_id, [], [
            'token' => [
                $token->token,
            ]
        ]);
        $response->assertStatus(400);
    }

    public function test_can_listar_materias_libres()
    {

        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $materia = $this->agregarMateriaGrado($grupo->grado);

        $response = $this->get('api/grupo/' . $grupo->idGrupo . '/materias-libres', [
            'token' => [
                $token->token,
            ]
        ]);

        $response->assertStatus(200);
        $response->assertSee($materia->nombre);
        $response->assertSee($materia->id);

    }
    public function test_error_listar_materias_libres_de_grupo_inexistente()
    {
        $token = token::factory()->create();
        $grupo_id = 123456; // id de grupo inexistente
        $response = $this->get('api/grupo/' . $grupo_id . '/materias-libres', [
            'token' => [
                $token->token,
            ]
        ]);
        $response->assertStatus(404);
    }
    public function agregarMateriaGrado($grado)
    {
        $token = token::factory()->create();

        $materia = materia::factory()->create();

        $materiaAgregar = [
            'materia_id' => $materia->id,
            'cantidad_horas' => "20"
        ];
        $response = $this->post('api/grado/' . $grado->id . "/materia", $materiaAgregar, [
            'token' => [
                $token->token
            ]
        ]);

        return $materia;
    }



//para hacer falta profesores y alumnos factory

// public function test_update_grupo()
// {

//     $token = token::factory()->create();
//     $grupo = grupos::factory()->create();
//     dd($grupo);

//     $response = $this->put('api/grupo/' . $grupo->idGrupo, [
//         'idGrupo' => Str::random(4),
//         "anioElectivo" => Carbon::now()->format('Y'),
//         "grado_id" => $grado->id,
//     ], [
//             'token' => [
//                 $token->token
//             ]
//         ]);

//     $response->assertStatus(200);
//     $data = $response->json();
//     $this->assertDatabaseHas('grupos', [
//         'idGrupo' => $data['idGrupo'],
//         'anioElectivo' => $data['anioElectivo'],
//         'grado_id' => $data['grado_id'],
//     ]);
// }




}