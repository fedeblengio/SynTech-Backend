<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\materia;
use Illuminate\Support\Facades\DB;

class TrayectoCompletoTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login()
    {

        $data = ['username' => '77777777','password'=>'1'];
        $response = $this->postJson('/api/login', $data);
        $response->assertStatus(200);
    }

    public function test_listar_usuarios()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/usuarios');  
        
        $response->assertStatus(200);
    }

    public function test_listar_un_usuario()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['username' => '77777777'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->getJson('/api/usuario', $data);
        
        $response->assertStatus(200);
    }

    public function test_listar_grupos()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/grupos');  
        
        $response->assertStatus(200);
    }

    public function test_agregar_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC','nombreCompleto'=>'Pruebas Grupo'];


        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/grupo', $data);
        
        $response->assertStatus(200);
    }

    public function test_listar_un_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->getJson('/api/grupo', $data);  
        
        $response->assertStatus(200);
    }

    public function test_agregar_grupo_existente()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC','nombreCompleto'=>'Pruebas Grupo'];


        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/grupo', $data);
        
        $response->assertStatus(416);
    } 

    public function test_modificar_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'ABC', 'nuevoGrupo' => 'CBA' ,'nuevoNombreCompleto' =>'Grupo Pruebas' ]; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->putJson('/api/grupo', $data);
        
        $response->assertStatus(200);
    } 

    public function test_listar_alumnos_sin_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/alumnos');  
        
        $response->assertStatus(200);
    }

    public function test_agregar_alumno_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'CBA' , 'idAlumnos' => '88888888'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/alumno', $data);  
        
        $response->assertStatus(200);
    } 

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

   

    public function test_listar_materias_sin_profesor()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idProfesor' => ''];
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/profesor');  
        
        $response->assertStatus(200);
    }

    public function test_listar_profesores()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/profesores');  
        
        $response->assertStatus(200);
    }

    public function test_agregar_materia_profesor()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Netherite')->first();
        $data = ['idMateria' => $materia->id , 'idProfesor' => '77777777'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/profesor', $data);  
        
        $response->assertStatus(200);
    } 

    public function test_agregar_materia_profesor_igual()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Netherite')->first();
        $data = ['idMateria' => $materia->id , 'idProfesor' => '77777777'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/profesor', $data);  
        
        $response->assertStatus(406);
    } 

    public function test_listar_materias_sin_profesores()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $data = ['idGrupo' => 'CBA' , 'idProfesor' => '77777777'];
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/grupo-materia', $data);  
        
        $response->assertStatus(200);
    }

    public function test_mostrar_profesor_materia()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/profesorMateria');  
        
        $response->assertStatus(200);
    }

    public function test_mostrar_profesores_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $response = $this->withHeaders([
            'token' => $token,
        ])->get('/api/curso');  
        
        $response->assertStatus(200);
    }

    public function test_crear_grupo_profesor_foro()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Netherite')->first();
        $data = ['idGrupo' => 'CBA' , 'idProfesor' => '77777777', 'idMateria' => $materia->id];
        $response = $this->withHeaders([
            'token' => $token,
        ])->postJson('/api/curso', $data);  
        
        $response->assertStatus(200);
    }

    public function test_eliminar_grupo_profesor_foro()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $idForo = DB::table('foros')->orderBy('created_at', 'desc')->limit(1)->get('id');
       
        $data = ['idForo' => $idForo[0]->id]; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/grupoForo', $data);
        
        $response->assertStatus(200);
    }

    public function test_eliminar_foro()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $idForo = DB::table('foros')->orderBy('created_at', 'desc')->limit(1)->get('id');
       
        $data = ['idForo' => $idForo[0]->id]; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/foro', $data);
        
        $response->assertStatus(200);
    }

    public function test_eliminar_profesor_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Netherite')->first();
       
        $data = ['idMateria' => $materia->id , 'idProfesor' => '77777777' , 'idGrupo' => 'CBA']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/curso', $data);
        
        $response->assertStatus(200);
    }

    public function test_eliminar_alumno_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
       
        $data = ['idAlumnos' => '88888888']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/alumno', $data);
        
        $response->assertStatus(200);
    }




    public function test_eliminar_materia_profesor()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        $materia = materia::where('nombre', 'Netherite')->first();
        $data = ['idMateria' => $materia->id , 'idProfesor' => '77777777']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/profesor', $data);
        
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

    public function test_eliminar_grupo()
    {
        $token = "c3ludGVjaDIwMjEuZGRucy5uZXQ=";
        
        $data = ['idGrupo' => 'CBA']; 
        

        $response = $this->withHeaders([
            'token' => $token,
        ])->deleteJson('/api/grupo', $data);
        
        $response->assertStatus(200);
    }




}
