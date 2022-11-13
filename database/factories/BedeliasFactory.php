<?php

namespace Database\Factories;
use App\Models\Bedelias;
use App\Models\User;
use UserFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
class BedeliasFactory extends Factory
{
    protected $model = Bedelias::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory();
    
        return [
            'id' => $user->id,
            'cedula_bedelia' => $user->id,
            'cargo' => "administrador",
        ];
    }
}
