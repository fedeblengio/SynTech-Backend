<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Carrera;
use App\Models\token;


class CarreraControllerTest extends TestCase
{
    // use RefreshDatabase;
    use WithFaker;


    public function test_can_show_carrera()
    {
        $token = token::factory()->create();
        // Create a Carrera using the factory
        $carrera = Carrera::factory()->create();
        
        // Send a GET request to the show method of the CarreraController
        $response = $this->get('api/carrera/' . $carrera->id, [
            'headers' => [
                'token' => $token,
            ],
        ]);

        $response->assertStatus(200);
        $response->assertSee($carrera->nombre);
        $response->assertSee($carrera->plan);
        $response->assertSee($carrera->categoria);
    }

    public function test_can_list_all_carreras()
    {
        $token = token::factory()->create();
        $carrera1 = Carrera::factory()->create();
        $carrera2 = Carrera::factory()->create();
    
        $response = $this->get('api/carrera', [
            'headers' => [
                'token' => $token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($carrera1->nombre);
        $response->assertSee($carrera2->nombre);
        $response->assertSee($carrera1->plan);
        $response->assertSee($carrera2->plan);
        $response->assertSee($carrera1->categoria);
        $response->assertSee($carrera2->categoria);
    }

    
}