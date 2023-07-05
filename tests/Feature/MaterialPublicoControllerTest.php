<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

use Illuminate\Support\Str;

use App\Models\token;
use App\Models\MaterialPublico;
use App\Models\usuarios;
use App\Models\bedelias;
use App\Models\material_publico;
use LdapRecord\Models\ActiveDirectory\User;


class MaterialPublicoControllerTest extends TestCase
{


    use RefreshDatabase;

    public function testRequestSinToken()
    {
        $response = $this->get('api/noticia');
        $response->assertStatus(401);
    }

    public function testCreateNoticia()
    {
        $token = token::factory()->create();
        $credentials = $this->createNewUser();

        $nuevaNoticia = [
            'idUsuario' => $credentials['username'],
            'titulo' => Str::random(10),
            'mensaje' => Str::random(10),
        ];
        $response = $this->post('api/noticia', $nuevaNoticia,[
            'token' => [
                $token->token,
            ],
        ]);
  
        $response->assertStatus(201);
        $response->assertSee($nuevaNoticia['idUsuario']);
        $response->assertSee($nuevaNoticia['titulo']);
        $response->assertSee($nuevaNoticia['mensaje']);
    }

    public function testIndexNoticia()
    {
        $token = token::factory()->create();
        $credentials = $this->createNewUser();
        $nuevaNoticia = material_publico::factory()->create([
            'idUsuario' => $credentials['username'],
        ]);
        $response = $this->get('api/noticia',[
            'token' => [
                $token->token,
            ],
        ]);
        $response->assertStatus(200);
        $response->assertSee($nuevaNoticia['idUsuario']);
        $response->assertSee($nuevaNoticia['titulo']);
        $response->assertSee($nuevaNoticia['mensaje']);
    }

    public function testDestroyNoticia()
    {
        $token = token::factory()->create();
        $credentials = $this->createNewUser();
        $nuevaNoticia = material_publico::factory()->create([
            'idUsuario' => $credentials['username'],
        ]);
        $response = $this->delete('api/noticia/' . $nuevaNoticia->id,[],[
            'token' => [
                $token->token,
            ],
        ]);
        $this->assertDeleted('material_publicos', [
            'idUsuario' => $nuevaNoticia['idUsuario'],
            'titulo' => $nuevaNoticia['titulo'],
            'mensaje' => $nuevaNoticia['mensaje'],
        ]);
        $response->assertStatus(200);
    }

    public function testErrorCreateNoticia()
    {
        $token = token::factory()->create();

        $nuevaNoticia = [
            'idUsuario' => '121211212121212',
            'titulo' => Str::random(10),
        ];
        $response = $this->post('api/noticia', $nuevaNoticia,[
            'token' => [
                $token->token,
            ],
        ]);
        $this->assertDatabaseMissing('material_publicos', [
            'idUsuario' => $nuevaNoticia['idUsuario'],
            'titulo' => $nuevaNoticia['titulo'],
        ]);
        $response->assertStatus(302);
    }

    public function testErrorDestroyNoticia()
    {
        $randomString = Str::random(10);
        $token = token::factory()->create();
        $response = $this->delete('api/noticia/' . $randomString,[],[
            'token' => [
                $token->token,
            ],
        ]);
        $this->assertDatabaseMissing('material_publicos', [
            'idUsuario' => $randomString,
        ]);
        $response->assertStatus(404);
    }

    private function createNewuser(){
        $padded_number = str_pad(mt_rand(1, 9999999), 1 - strlen('1'), '0', STR_PAD_LEFT);
        $randomID = "1". $padded_number;
       
        $user = usuarios::factory()->create([
            'id' => $randomID,
            'ou' => 'Bedelias'
        ]);
        $bedelias = bedelias::factory()->create([
            'id' => $randomID,
            'Cedula_Bedelia' =>$randomID,
            'cargo' => 'administrador'
        ]);

        return ['username' => $randomID, 'password' => $randomID];

    }

}
