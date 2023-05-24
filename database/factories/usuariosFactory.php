<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class UsuariosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
         return [
            'id' => $this->faker->unique()->randomNumber($nbDigits = 8),
            'nombre' => $this->faker->name,
            'email' =>$this->faker->unique()->safeEmail,
            'ou' => 'Alumno',
            'imagen_perfil' => "default_picture.png",
            'genero' => "",
        ];
    }
}
