<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InscripcionEquipo>
 */
class InscripcionEquipoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'torneo_id' => \App\Models\Torneo::factory(),
            'categoria_id' => \App\Models\Categoria::factory(),
            'lider_jugador_id' => \App\Models\Jugador::factory(),
            'estado' => 'pendiente',
            'expires_at' => now()->addMinutes(10),
            'equipo_id' => null,
            'cancelado_por' => null,
            'nombre_equipo' => null,
        ];
    }
}
