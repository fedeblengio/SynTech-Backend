<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Carrera;
class CarreraFactory extends Factory
{
    protected $model = Carrera::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'nombre' => $this->faker->word,
            'plan' =>$this->faker->year(),
            'categoria'=>$this->faker->randomElement(['Informatica','Disenio Web','Arte','Mecanica','Arquitectura']),
        ];
        // 'nombre' => "A",
        // 'plan' =>"2003",
        // 'categoria'=>"A",
    }
}
