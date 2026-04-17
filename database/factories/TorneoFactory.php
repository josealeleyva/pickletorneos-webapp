<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Torneo>
 */
class TorneoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->words(3, true),
            'deporte_id' => \App\Models\Deporte::create(['nombre' => $this->faker->word()])->id,
            'complejo_id' => \App\Models\ComplejoDeportivo::factory(),
            'organizador_id' => \App\Models\User::factory(),
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(20),
            'estado' => 'activo',
        ];
    }
}
