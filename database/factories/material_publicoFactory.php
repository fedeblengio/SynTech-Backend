<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\material_publico;

class material_publicoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    protected $model = material_publico::class;
    public function definition()
    {
        return [
            'titulo' => $this->faker->sentence($nbWords = 6, $variableNbWords = true),
            'mensaje' => $this->faker->paragraph($nbSentences = 3, $variableNbSentences = true),
            'idUsuario' => $this->faker->randomNumber($nbDigits = 8),
            'imgEncabezado' => "encabezadoPredeterminado.jpg"
        ];
    }
}
