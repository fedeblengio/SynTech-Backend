<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Storage;
use App\Models\token;

class ImportarMateriaTest extends TestCase
{
 
    use RefreshDatabase;

    public function testImportFromCSV()
    {
        $token = token::factory()->create();
        $csvPath = storage_path('app/Files/materias.csv');
        $response = $this->post('api/materia/importar', [
            'file' => new \Illuminate\Http\UploadedFile($csvPath, 'materias.csv', 'text/csv', null, true)
        ], [
                'token' => [
                    $token->token
                ]
            ]);
        $response->assertStatus(200);

        $this->assertDatabaseHas('materias', [
            'nombre' => 'Fisica-Quantica'
        ]);
    }
}
