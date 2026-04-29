<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Juego>
 */
class JuegoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'partido_id' => \App\Models\Partido::factory(),
            'juegos_equipo1' => 11,
            'juegos_equipo2' => 7,
            'orden' => 1,
        ];
    }
}
