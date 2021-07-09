<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\gruposController;
use App\Models\grupos;
use Tests\TestCase;
use Illuminate\Http\Request;

use function PHPUnit\Framework\assertJson;

class pruebaaTest extends TestCase{

    public function test_agregar_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo'   => 'TB9',
            'nombreCompleto' => 'Prueba6',
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

    public function test_show_grupo()
    {
        $this->withoutExceptionHandling();
        $request = new Request([
            'idGrupo'   => 'TB1',
        ]);
        $grupos = new gruposController();
        $resultado = $grupos->show($request);
        $salida = json_encode($resultado);
        var_dump($salida);
        
        $this->assertTrue(true);
        
    } 




}



