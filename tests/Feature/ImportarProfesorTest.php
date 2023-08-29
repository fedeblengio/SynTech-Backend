<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\token;
use LdapRecord\Models\ActiveDirectory\User;
class ImportarProfesorTest extends TestCase
{
    use RefreshDatabase;
    public function testImportFromCSV()
    {
        $token = token::factory()->create();
        $csvPath = storage_path('app/Files/profesores.csv');
        $response = $this->post('api/profesor/importar', [
            'file' => new \Illuminate\Http\UploadedFile($csvPath, 'profesores.csv', 'text/csv', null, true)
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $this->assertDatabaseHas('usuarios', [
            'email' => 'Addia.Kenney@gmail.com'
        ]);
        $this->deleteCreatedLDAPUser('99961492');
    }
    public function deleteCreatedLDAPUser($samaccountname)
    {
        try {
            $user = User::find('cn=' . $samaccountname . ',ou=Testing,dc=syntech,dc=intra');
            $user->delete();
        } catch (\Exception $e) {
            return null;
        }

    }
}
