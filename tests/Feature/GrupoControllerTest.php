<?php

namespace Tests\Feature;

use App\Models\alumnos;
use App\Models\Grado;
use App\Models\grupos;
use App\Models\materia;
use App\Models\profesores;
use App\Models\token;
use App\Models\usuarios;
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
   
    public function testRequestSinToken()
    {
        $response = $this->get('api/grupo/');
        $response->assertStatus(401);
    }
    public function testCanMostrarGrupo()
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
    public function testErrorMostrarGrupoNoExistente()
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

    public function testCanCrearGrupo()
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
    public function testErrorCrearGrupo()
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
        $this->assertDatabaseMissing('grupos', [
            'anioElectivo' => Carbon::now()->format('Y'),
            'grado_id' => $grado->id,
        ]);
    }

    public function testCanEliminarGrupo()
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
    public function testErrorEliminarGrupoInexistente()
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

    public function testCanListarMateriasLibres()
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
    public function testErrorListarMateriasLibresDeGrupoInexistente()
    {
        $token = token::factory()->create();
        $grupo_id = 123456; 
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


    public function testUpdateGrupoAlumno()
    {

        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
      
        $data = [
            [
                'idGrupo' => $grupo->idGrupo,
                'idAlumno' => $this->crearUsuarioNuevoAlumno(),
            ]
        ];
        $response = $this->put('api/grupo/' . $grupo->idGrupo, [
            "anioElectivo" => Carbon::now()->format('Y'),
            "grado_id" => $grupo->grado_id,
            "alumnos" => $data,
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(200);
        $this->assertEquals($response['alumnos'][0]['id'], $data[0]['idAlumno']);
        $this->assertDatabaseHas('alumnos_pertenecen_grupos', [
            'idAlumnos' => $data[0]['idAlumno'],
            'idGrupo' => $grupo->idGrupo,
        ]);
    }
    public function testUpdateGrupoProfesor()
    {

        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $materia = materia::factory()->create();
        $data = [
                'idProfesor' => $this->crearUsuarioNuevoProfesor(),
                'idMateria' => $materia->id,
                'idGrupo' =>$grupo->idGrupo
        ];

       
        $response = $this->put('api/grupo/' . $grupo->idGrupo, [
            "anioElectivo" => Carbon::now()->format('Y'),
            "grado_id" => $grupo->grado_id,
            "profesores" => $data,
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('grupos_tienen_profesor',[ 
            'idProfesor' => $data['idProfesor'],
            'idGrupo' => $grupo->idGrupo,
            'idMateria' => $data['idMateria'],
        ]);
        $this->assertEquals($response['profesores'][0]['id'], $data['idProfesor']);
    }

    public function testAlumnosNoPertencenGrupo(){
        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $alumno = $this->crearUsuarioNuevoAlumno();
        $response = $this->get("api/grupo/{id}/alumnos", [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee($alumno);
    }

    public function crearUsuarioNuevoAlumno()
    {
        $randomID = str_pad(mt_rand(10000000, 99999999), 7);
        $user = usuarios::factory()->create([
            'id' => $randomID,
            'ou' => 'Alumno'
        ]);

        $alumnos = alumnos::factory()->create([
            'id' => $randomID,
            'Cedula_Alumno' => $randomID,
        ]);

        return $randomID;
    }
    public function crearUsuarioNuevoProfesor()
    {
        $randomID = str_pad(mt_rand(10000000, 99999999), 7);

        $user = usuarios::factory()->create([
            'id' => $randomID,
            'ou' => 'Profesor'
        ]);

        $profeosr = profesores::factory()->create([
            'id' => $randomID,
            'Cedula_Profesor' => $randomID,
        ]);

     

       
        return $randomID;
    }

    
    public function testDeleteGrupoAlumno()
    {
        $token = token::factory()->create();
        $info = $this->crearAlumnoGrupo();
        $response = $this->delete("api/grupo/".$info['grupo']['idGrupo']."/alumno/".$info['alumno']['idAlumno'], [],[
            'token' => [
                $token->token,
            ],
        ]);
        $this->assertDatabaseMissing('alumnos_pertenecen_grupos', [
            'idAlumnos' => $info['alumno']['idAlumno'],
            'idGrupo' => $info['grupo']['idGrupo'],
        ]);

        $response->assertStatus(200);
        
    }
    public function testErrorDeleteGrupoAlumno()
    {
        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
     
        $response = $this->delete("api/grupo/".$grupo->id."/alumno/randomUser", [],[
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(400);
        
    }
    public function crearAlumnoGrupo(){
        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $data = [
            [
                'idGrupo' => $grupo->idGrupo,
                'idAlumno' => $this->crearUsuarioNuevoAlumno(),
            ]
        ];
        $response = $this->put('api/grupo/' . $grupo->idGrupo, [
            "anioElectivo" => Carbon::now()->format('Y'),
            "grado_id" => $grupo->grado_id,
            "alumnos" => $data,
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        return [
            'grupo' => $grupo,
            'alumno' => $data[0]
        ];
    }
    public function testDeleteGrupoProfesor()
    {
        $token = token::factory()->create();
        $info = $this->crearProfesorGrupo();
        $response = $this->delete("api/grupo/".$info['grupo']['idGrupo']."/profesor/".$info['profesor']['idProfesor'],[], [
            'token' => [
                $token->token,
            ],
        ]);
        $this->assertDatabaseMissing('grupos_tienen_profesor', [
            'idProfesor' => $info['profesor']['idProfesor'],
            'idGrupo' => $info['grupo']['idGrupo'],
        ]);
        $response->assertStatus(200);
        
    }

    public function testErrorDeleteGrupoProfesor()
    {
        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $response = $this->delete("api/grupo/".$grupo->id."/profesor/"."randomID",[], [
            'token' => [
                $token->token,
            ],
        ]);

        $response->assertStatus(400);
        
    }

    public function crearProfesorGrupo(){
        $token = token::factory()->create();
        $grupo = grupos::factory()->create();
        $materia = materia::factory()->create();
        $data = [
                'idProfesor' => $this->crearUsuarioNuevoProfesor(),
                'idMateria' => $materia->id,
                'idGrupo' =>$grupo->idGrupo
        ];

        $response = $this->put('api/grupo/' . $grupo->idGrupo, [
            "anioElectivo" => Carbon::now()->format('Y'),
            "grado_id" => $grupo->grado_id,
            "profesores" => $data,
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $response->assertStatus(200);
        
        return [
            'grupo' => $grupo,
            'profesor' => $data
        ];
    }
}