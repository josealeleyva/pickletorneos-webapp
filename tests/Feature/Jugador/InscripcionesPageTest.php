<?php

namespace Tests\Feature\Jugador;

use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
use App\Models\Jugador;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InscripcionesPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_user_is_redirected_to_login(): void
    {
        $response = $this->get('/jugador/inscripciones');

        $response->assertRedirect('/login');
    }

    public function test_authenticated_user_without_jugador_profile_sees_empty_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $response->assertStatus(200);
        $response->assertViewIs('jugador.inscripciones');
        $response->assertViewHas('invitacionesPendientes');
        $response->assertViewHas('historialInvitaciones');
        $response->assertViewHas('inscripcionesPendientes');
        $response->assertViewHas('inscripcionesConfirmadas');
        $this->assertCount(0, $response->viewData('invitacionesPendientes'));
        $this->assertCount(0, $response->viewData('inscripcionesPendientes'));
    }

    public function test_page_shows_pending_invitations_for_jugador(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        $invitacion = InvitacionJugador::factory()->create([
            'jugador_id' => $jugador->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $response->assertStatus(200);
        $pendientes = $response->viewData('invitacionesPendientes');
        $this->assertCount(1, $pendientes);
        $this->assertEquals($invitacion->id, $pendientes->first()->id);
    }

    public function test_page_shows_last_10_historical_invitations(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        // Crear 12 invitaciones respondidas
        InvitacionJugador::factory()->count(12)->create([
            'jugador_id' => $jugador->id,
            'estado' => 'aceptada',
            'respondido_at' => now(),
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $historial = $response->viewData('historialInvitaciones');
        $this->assertCount(10, $historial);
    }

    public function test_page_shows_inscriptions_led_by_jugador(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        $inscripcion = InscripcionEquipo::factory()->create([
            'lider_jugador_id' => $jugador->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $pendientes = $response->viewData('inscripcionesPendientes');
        $this->assertCount(1, $pendientes);
        $this->assertEquals($inscripcion->id, $pendientes->first()->id);
    }

    public function test_page_does_not_show_other_jugadores_invitations(): void
    {
        $user = User::factory()->create();
        $jugador = Jugador::factory()->create(['user_id' => $user->id]);

        $otroJugador = Jugador::factory()->create();
        InvitacionJugador::factory()->create([
            'jugador_id' => $otroJugador->id,
            'estado' => 'pendiente',
        ]);

        $response = $this->actingAs($user)->get('/jugador/inscripciones');

        $this->assertCount(0, $response->viewData('invitacionesPendientes'));
    }
}
