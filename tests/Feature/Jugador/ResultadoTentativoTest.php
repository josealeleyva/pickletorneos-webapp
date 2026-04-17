<?php

namespace Tests\Feature\Jugador;

use App\Models\Equipo;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\ResultadoTentativo;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ResultadoTentativoTest extends TestCase
{
    use RefreshDatabase;

    private function crearEscenario(): array
    {
        $torneo = Torneo::factory()->create(['estado' => 'en_curso']);

        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $jugador1 = Jugador::factory()->create(['user_id' => $user1->id]);
        $jugador2 = Jugador::factory()->create(['user_id' => $user2->id]);

        $equipo1 = Equipo::create(['nombre' => 'Equipo A', 'torneo_id' => $torneo->id]);
        $equipo2 = Equipo::create(['nombre' => 'Equipo B', 'torneo_id' => $torneo->id]);

        $equipo1->jugadores()->attach($jugador1->id);
        $equipo2->jugadores()->attach($jugador2->id);

        $partido = Partido::create([
            'fecha_hora' => now()->subHour(),
            'equipo1_id' => $equipo1->id,
            'equipo2_id' => $equipo2->id,
            'estado' => 'programado',
        ]);

        return compact('torneo', 'user1', 'user2', 'jugador1', 'jugador2', 'equipo1', 'equipo2', 'partido');
    }

    private function juegosPayload(): array
    {
        return [
            'juegos' => [
                ['juego_equipo1' => 6, 'juego_equipo2' => 3],
                ['juego_equipo1' => 6, 'juego_equipo2' => 4],
            ],
        ];
    }

    public function test_jugador_puede_proponer_resultado_tentativo(): void
    {
        $e = $this->crearEscenario();

        $response = $this->actingAs($e['user1'])
            ->post("/jugador/partidos/{$e['partido']->id}/resultado", $this->juegosPayload());

        $response->assertRedirect('/jugador/partidos');
        $this->assertDatabaseHas('resultados_tentativo', [
            'partido_id' => $e['partido']->id,
            'propuesto_por_equipo_id' => $e['equipo1']->id,
            'propuesto_por_jugador_id' => $e['jugador1']->id,
            'sets_equipo1' => 12,
            'sets_equipo2' => 7,
            'equipo_ganador_id' => $e['equipo1']->id,
        ]);
    }

    public function test_no_puede_proponer_si_partido_no_termino(): void
    {
        $e = $this->crearEscenario();
        $e['partido']->update(['fecha_hora' => now()->addHour()]);

        $response = $this->actingAs($e['user1'])
            ->post("/jugador/partidos/{$e['partido']->id}/resultado", $this->juegosPayload());

        $response->assertSessionHasErrors();
        $this->assertDatabaseEmpty('resultados_tentativo');
    }

    public function test_no_puede_proponer_si_partido_ya_tiene_resultado_oficial(): void
    {
        $e = $this->crearEscenario();
        $e['partido']->update(['equipo_ganador_id' => $e['equipo1']->id]);

        $response = $this->actingAs($e['user1'])
            ->post("/jugador/partidos/{$e['partido']->id}/resultado", $this->juegosPayload());

        $response->assertSessionHasErrors();
        $this->assertDatabaseEmpty('resultados_tentativo');
    }

    public function test_no_puede_proponer_si_tentativo_ya_existe(): void
    {
        $e = $this->crearEscenario();

        ResultadoTentativo::create([
            'partido_id' => $e['partido']->id,
            'propuesto_por_equipo_id' => $e['equipo1']->id,
            'propuesto_por_jugador_id' => $e['jugador1']->id,
            'juegos' => [['juego_equipo1' => 6, 'juego_equipo2' => 3]],
            'sets_equipo1' => 6,
            'sets_equipo2' => 3,
            'equipo_ganador_id' => $e['equipo1']->id,
        ]);

        $response = $this->actingAs($e['user1'])
            ->post("/jugador/partidos/{$e['partido']->id}/resultado", $this->juegosPayload());

        $response->assertSessionHasErrors();
        $this->assertDatabaseCount('resultados_tentativo', 1);
    }

    public function test_jugador_rival_puede_confirmar_resultado(): void
    {
        $e = $this->crearEscenario();

        $resultado = ResultadoTentativo::create([
            'partido_id' => $e['partido']->id,
            'propuesto_por_equipo_id' => $e['equipo1']->id,
            'propuesto_por_jugador_id' => $e['jugador1']->id,
            'juegos' => [
                ['juego_equipo1' => 6, 'juego_equipo2' => 3],
                ['juego_equipo1' => 6, 'juego_equipo2' => 4],
            ],
            'sets_equipo1' => 12,
            'sets_equipo2' => 7,
            'equipo_ganador_id' => $e['equipo1']->id,
        ]);

        $response = $this->actingAs($e['user2'])
            ->post("/jugador/resultados/{$resultado->id}/confirmar");

        $response->assertRedirect('/jugador/partidos');

        $partido = $e['partido']->fresh();
        $this->assertEquals(12, $partido->sets_equipo1);
        $this->assertEquals(7, $partido->sets_equipo2);
        $this->assertEquals($e['equipo1']->id, $partido->equipo_ganador_id);
        $this->assertEquals('finalizado', $partido->estado);
        $this->assertDatabaseEmpty('resultados_tentativo');
        $this->assertDatabaseCount('juegos', 2);
    }

    public function test_jugador_proponente_no_puede_confirmar_su_propio_resultado(): void
    {
        $e = $this->crearEscenario();

        $resultado = ResultadoTentativo::create([
            'partido_id' => $e['partido']->id,
            'propuesto_por_equipo_id' => $e['equipo1']->id,
            'propuesto_por_jugador_id' => $e['jugador1']->id,
            'juegos' => [['juego_equipo1' => 6, 'juego_equipo2' => 3]],
            'sets_equipo1' => 6,
            'sets_equipo2' => 3,
            'equipo_ganador_id' => $e['equipo1']->id,
        ]);

        $response = $this->actingAs($e['user1'])
            ->post("/jugador/resultados/{$resultado->id}/confirmar");

        $response->assertForbidden();
        $this->assertDatabaseHas('resultados_tentativo', ['id' => $resultado->id]);
    }

    public function test_jugador_rival_puede_modificar_resultado(): void
    {
        $e = $this->crearEscenario();

        $resultado = ResultadoTentativo::create([
            'partido_id' => $e['partido']->id,
            'propuesto_por_equipo_id' => $e['equipo1']->id,
            'propuesto_por_jugador_id' => $e['jugador1']->id,
            'juegos' => [['juego_equipo1' => 6, 'juego_equipo2' => 3]],
            'sets_equipo1' => 6,
            'sets_equipo2' => 3,
            'equipo_ganador_id' => $e['equipo1']->id,
        ]);

        $response = $this->actingAs($e['user2'])
            ->post("/jugador/resultados/{$resultado->id}/modificar", [
                'juegos' => [
                    ['juego_equipo1' => 3, 'juego_equipo2' => 6],
                    ['juego_equipo1' => 4, 'juego_equipo2' => 6],
                ],
            ]);

        $response->assertRedirect('/jugador/partidos');

        $resultado->refresh();
        $this->assertEquals($e['equipo2']->id, $resultado->propuesto_por_equipo_id);
        $this->assertEquals($e['jugador2']->id, $resultado->propuesto_por_jugador_id);
        $this->assertEquals(7, $resultado->sets_equipo1);
        $this->assertEquals(12, $resultado->sets_equipo2);
        $this->assertEquals($e['equipo2']->id, $resultado->equipo_ganador_id);
    }

    public function test_pagina_partidos_requiere_autenticacion(): void
    {
        $response = $this->get('/jugador/partidos');
        $response->assertRedirect('/login');
    }

    public function test_pagina_partidos_muestra_pendientes_de_confirmacion(): void
    {
        $e = $this->crearEscenario();

        ResultadoTentativo::create([
            'partido_id' => $e['partido']->id,
            'propuesto_por_equipo_id' => $e['equipo1']->id,
            'propuesto_por_jugador_id' => $e['jugador1']->id,
            'juegos' => [['juego_equipo1' => 6, 'juego_equipo2' => 3]],
            'sets_equipo1' => 6,
            'sets_equipo2' => 3,
            'equipo_ganador_id' => $e['equipo1']->id,
        ]);

        // user2 (equipo2, el rival) debe ver el partido en "pendientes de confirmación"
        $response = $this->actingAs($e['user2'])->get('/jugador/partidos');

        $response->assertStatus(200);
        $response->assertViewIs('jugador.partidos');
        $pendientes = $response->viewData('pendientesConfirmacion');
        $this->assertCount(1, $pendientes);
    }
}
