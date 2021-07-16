<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Http\Request;
use App\Http\Controllers\loginController;
use App\Http\Controllers\usuariosController;
use LdapRecord\Connection;


class loginTest extends TestCase
{
    
    public function test_login()
    {
        $this->withoutExceptionHandling();

        $request2 = new Request([
            'username' => '19191919',
            'password' => '1',
        ]);

        $login = new loginController();
        $resultado = $login->connect($request2);
        $salida3 = json_encode($resultado);
        var_dump($salida3);
        $this->assertTrue(true);
    } 
 
 } 
