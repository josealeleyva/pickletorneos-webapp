<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\InvitacionJugador>
 */
class InvitacionJugadorFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'inscripcion_equipo_id' => \App\Models\InscripcionEquipo::factory(),
            'jugador_id' => \App\Models\Jugador::factory(),
            'estado' => 'pendiente',
            'auto_aceptada' => false,
            'token' => \Illuminate\Support\Str::random(40),
            'respondido_at' => null,
        ];
    }
}
