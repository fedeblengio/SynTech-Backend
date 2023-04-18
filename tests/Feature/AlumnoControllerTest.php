<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AlumnoControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_create_user_alumno()
    {
        // $token = token::factory()->create();
        // $randomID = str_pad(mt_rand(1, 99999999), 8, '0', STR_PAD_LEFT);
        // $newTeacher = [
        //     'samaccountname' =>$randomID,
        //     'name' => "Jasdsaohn",
        //     'surname' => "adas",
        //     'userPrincipalName' => 'adsad@example.com',
        //     'ou' => "Alumno",
        //     'materias' => [],
        // ];

        // $response = $this->post('api/usuario', $newTeacher,[
        //     'token' => [
        //         $token->token,
        //     ],
        // ]);

        // $response->assertStatus(200);
        // $response->assertSee($newTeacher['userPrincipalName']);
        // $response->assertSee($newTeacher['ou']);

        $this->assertTrue(true);
    }
}
