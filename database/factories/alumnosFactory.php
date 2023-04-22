<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class alumnosFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $random= $this->faker->unique()->randomNumber($nbDigits = 8);
        return [
            'id' => $random,
            'Cedula_Alumno' => $random,
        ];
    }
}
