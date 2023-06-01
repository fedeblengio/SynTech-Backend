<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\token;
use LdapRecord\Models\ActiveDirectory\User;
class ImportarBedeliaTest extends TestCase
{
    use RefreshDatabase;
    public function testImportFromCSV()
    {
        $token = token::factory()->create();
        $csvPath = storage_path('app/Files/bedelias.csv');
        $response = $this->post('api/bedelia/importar', [
            'file' => new \Illuminate\Http\UploadedFile($csvPath, 'bedelias.csv', 'text/csv', null, true)
        ], [
                'token' => [
                    $token->token
                ]
            ]);

        $this->assertDatabaseHas('usuarios', [
            'email' => 'Dawn.Gaynor@gmail.com'
        ]);
        $this->deleteCreatedLDAPUser('99984162');
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
