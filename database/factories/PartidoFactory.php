<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partido>
 */
class PartidoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'equipo1_id' => \App\Models\Equipo::factory(),
            'equipo2_id' => \App\Models\Equipo::factory(),
            'estado' => 'finalizado',
        ];
    }
}
