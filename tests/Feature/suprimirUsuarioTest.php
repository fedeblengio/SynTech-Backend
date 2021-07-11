<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use App\Http\Controllers\gruposController;
use App\Models\grupos;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\usuariosController;

use function PHPUnit\Framework\assertJson;

class suprimirUsuarioTest extends TestCase{

    public function test_eliminar_usuario()
    {
        $this->withoutExceptionHandling();

        $request = new Request([
            'username'   => '19191919',
            'ou'=> 'Profesor'
            
        ]);
        

        $user = new usuariosController();
        $resultado = $user->destroy($request);
        $salida4 = json_encode($resultado);
        var_dump($salida4);
        $this->assertTrue(true);
    } 





}



