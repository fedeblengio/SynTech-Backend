<?php

namespace Tests\Feature;

use App\Models\Grado;
use App\Models\grupos;
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


    //  Route::get('/grupo', 'App\Http\Controllers\gruposController@index');
    
    //  Route::put('/grupo/{id}', 'App\Http\Controllers\gruposController@update');

    //  Route::get('/grupo/{id}/alumnos', 'App\Http\Controllers\gruposController@alumnosNoPertenecenGrupo');
    //  Route::get('/grupo/{id}/materias-libres', 'App\Http\Controllers\gruposController@listarMateriasSinProfesor');
    //  Route::delete('/grupo/{id}', 'App\Http\Controllers\gruposController@destroy');
    //  Route::delete('/grupo/{id}/alumno/{idAlumno}', 'App\Http\Controllers\gruposController@eliminarAlumnoGrupo');
    //  Route::delete('/grupo/{id}/profesor/{idProfesor}', 'App\Http\Controllers\gruposController@eliminarProfesorGrupo');
  
    public function test_request_sin_token()
    {
        $response = $this->get('api/grupo/');
        $response->assertStatus(401);
    }

    public function test_can_show_grupo()
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

    public function test_can_create_grupo()
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

    //para hacer falta profesores y alumnos factory

    // public function test_update_grupo(){

    //     $token = token::factory()->create();
    //     $grupo = grupos::factory()->create();
    //     dd($grupo);
    //     $grado = Grado::Factory()->create();

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

        
    



