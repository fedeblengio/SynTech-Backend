<?php

namespace Database\Factories;

use LdapRecord\Models\ActiveDirectory\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'dn' => 'cn=' . $this->faker->name() .'ou=UsuarioSistema,dc=example,dc=com',
            'cn' => $this->faker->name(),
            'unicodePwd' => $this->faker->unique()->safeEmail(),
            'samaccountname' => now(),
            'userAccountControl'=> 66048,
        ];
    }


}
