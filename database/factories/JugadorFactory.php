<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class JugadorFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => fake()->firstName(),
            'apellido' => fake()->lastName(),
            'email' => fake()->unique()->safeEmail(),
            'telefono' => fake()->numerify('###########'),
            'dni' => fake()->numerify('########'),
            'foto' => null,
            'fecha_nacimiento' => fake()->dateTimeBetween('-50 years', '-18 years')->format('Y-m-d'),
            'genero' => fake()->randomElement(['masculino', 'femenino', 'otro']),
            'user_id' => null,
            'organizador_id' => null,
        ];
    }
}
