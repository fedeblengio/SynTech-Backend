<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;
use App\Http\Controllers\gruposController;
use App\Http\Controllers\agregarUsuarioGrupoController;
use App\Http\Controllers\agregarMateriaController;
use App\Models\materia;
use App\Http\Controllers\profesorDictaMateriaController;
use App\Http\Controllers\gruposTienenProfesorController;
use Illuminate\Support\Facades\DB;

class FuncionesCompletasTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_agregar_usuario()
    {
        $this->withoutExceptionHandling();

        $request = new Request([
            'samaccountname'   => '19191919',
            'unicodePwd' => '1',
            'cn'=> 'Unit Test',
            'userPrincipalName'=>'UnitTest@example.com',
            'ou'=> 'Profesor'
        ]);
             

        $user = new usuariosController();
        $resultado = $user->create($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
    }  


  public function test_listar_usuario()
    {
        $this->withoutExceptionHandling();

        $request = new Request([
            'username'   => '19191919'
        ]);
             

        $user = new usuariosController();
        $resultado = $user->show($request);
        $salida1 = json_encode($resultado);
        var_dump($salida1);
        $this->assertTrue(true);
    }  

    public function test_listar_usuarios()
    {
        $this->withoutExceptionHandling();

        $user = new usuariosController();
        $resultado = $user->index();
        $salida1 = json_encode($resultado);
        var_dump($salida1);
        $this->assertTrue(true);
    }  

     public function test_modificar_usuario()
    {
        $this->withoutExceptionHandling();

        $request = new Request([
            'username' => '19191919',
            'newPassword' => '2',
            'nuevoNombre'=> 'Unit Test Modified',
            'nuevoEmail'=>'UnitTestModified@example.com',
        ]);
             

        $user = new usuariosController();
        $resultado = $user->update($request);
        $salida2 = json_encode($resultado);
        var_dump($salida2);
        $this->assertTrue(true);
    } 
    
    public function test_agregar_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo'   => 'ABC',
            'nombreCompleto' => 'Pruebas Grupo',
        ]);
        $grupos = new gruposController();
        $resultado = $grupos->create($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
       
    }

    public function test_listar_grupo()
    {
        $this->withoutExceptionHandling();
        
        $grupos = new gruposController();
        $resultado = $grupos->index();
        $salida = json_encode($resultado);
        var_dump($salida);
       
       $this->assertTrue(true);
    }


    public function test_update_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo'   => 'ABC',
            'nuevoGrupo' => 'CBA',
            'nuevoNombreCompleto' => 'Grupo Pruebas',
        ]);
        $grupos = new gruposController();
        $resultado = $grupos->update($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_show_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo'   => 'CBA',
        ]);
        $grupos = new gruposController();
        $resultado = $grupos->show($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 


    public function test_listar_alumnos_sin_grupo()
    {
        $this->withoutExceptionHandling();
       
        $alumnos = new agregarUsuarioGrupoController();
        $resultado = $alumnos->index();
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 
    
    public function test_agregar_alumno_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo'   => 'CBA',
            'idAlumnos' => '10101030'
        ]);
        $alumnos = new agregarUsuarioGrupoController();
        $resultado = $alumnos->store($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_listar_materias()
    {
        $this->withoutExceptionHandling();
       
        $materias = new agregarMateriaController();
        $resultado = $materias->index();
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_agregar_materia()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'nombreMateria' => 'Obsidiana'
        ]);
        $materias = new agregarMateriaController();
        $resultado = $materias->store($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_listar_materia()
    {
        $this->withoutExceptionHandling();
        $materia = materia::where('nombre', 'Obsidiana')->first();
        $request = new Request([
            'idMateria' => $materia->id
        ]);
        $materias = new agregarMateriaController();
        $resultado = $materias->show($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_update_materia()
    {
        $this->withoutExceptionHandling();
        $materia = materia::where('nombre', 'Obsidiana')->first();
        $request = new Request([
            'idMateria' => $materia->id,
            'nuevoNombre' => 'Netherite'
        ]);
        $materias = new agregarMateriaController();
        $resultado = $materias->update($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_listar_materias_sin_profesor()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idProfesor' => '19191919'
            
        ]);
       
        $profesor = new profesorDictaMateriaController();
        $resultado = $profesor->index($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_listar_prfoesores()
    {
        $this->withoutExceptionHandling();
       
        $profesor = new profesorDictaMateriaController();
        $resultado = $profesor->listarProfesores();
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_agregar_materia_profesor()
    {
        $this->withoutExceptionHandling();
        $materia = materia::where('nombre', 'Netherite')->first();
        $request = new Request([
            'idMateria' => $materia->id,
            'idProfesor' => '19191919'
            
        ]);
       
        $profesor = new profesorDictaMateriaController();
        $resultado = $profesor->store($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_listar_materias_sin_profesores()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo' => 'CBA',
            'idProfesor' => '19191919'
            
        ]);
       
        $gruposMateria = new gruposTienenProfesorController();
        $resultado = $gruposMateria->index($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_mostrar_profesor_materia()
    {
        $this->withoutExceptionHandling();
       
        $profesorMateria = new gruposTienenProfesorController();
        $resultado = $profesorMateria->mostrarProfesorMateria();
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 
    
    public function test_mostrar_profesores_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo' => 'CBA',
        ]);
        $curso = new gruposTienenProfesorController();
        $resultado = $curso->show($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 

    public function test_crear_grupo_profesor_foro()
    {
        $this->withoutExceptionHandling();
        $materia = materia::where('nombre', 'Netherite')->first();
        $request = new Request([
            'idGrupo' => 'CBA',
            'idProfesor' => '19191919',
            'idMateria' => $materia->id
        ]);
        $curso = new gruposTienenProfesorController();
        $resultado = $curso->store($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_eliminar_grupo_profesor_foro()
    {
        $this->withoutExceptionHandling();
        $idForo = DB::table('foros')->orderBy('created_at', 'desc')->limit(1)->get('id'); 
        $foro = $idForo[0]->id;
        $request = new Request([
            'idForo' => $foro
        ]);
        $curso = new gruposTienenProfesorController();
        $resultado = $curso->eliminarProfesorGrupoForo($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_eliminar_foro()
    {
        $this->withoutExceptionHandling();
        $idForo = DB::table('foros')->orderBy('created_at', 'desc')->limit(1)->get('id'); 
        $foro = $idForo[0]->id;
        $request = new Request([
            'idForo' => $foro
        ]);
        $curso = new gruposTienenProfesorController();
        $resultado = $curso->eliminarForo($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_eliminar_profesor_grupo()
    {
        $this->withoutExceptionHandling();
        $materia = materia::where('nombre', 'Netherite')->first();
        $request = new Request([
            'idMateria' => $materia->id,
            'idProfesor' => '19191919',
            'idGrupo' => 'CBA'
        ]);
        $curso = new gruposTienenProfesorController();
        $resultado = $curso->destroy($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_eliminar_alumno_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idAlumnos' => '10101030'
        ]);
        $alumnos = new agregarUsuarioGrupoController();
        $resultado = $alumnos->destroy($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    
    public function test_eliminar_materia_profesor()
    {
        $this->withoutExceptionHandling();
        $materia = materia::where('nombre', 'Netherite')->first();
        $request = new Request([
            'idMateria' => $materia->id,
            'idProfesor' => '19191919'
        ]);
        $profesor = new profesorDictaMateriaController();
        $resultado = $profesor->destroy($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_eliminar_materia()
    {
        $this->withoutExceptionHandling();
        $materia = materia::where('nombre', 'Netherite')->first();
        $request = new Request([
            'idMateria' => $materia->id,
        ]);
        $materias = new agregarMateriaController();
        $resultado = $materias->destroy($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 

    public function test_destroy_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo'   => 'CBA',
        ]);
        $grupos = new gruposController();
        $resultado = $grupos->destroy($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertEquals($salida, '{"headers":{},"original":{"status":"Success"},"exception":null}');
        
    } 





    









    
    
}
