<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use App\Services\InscripcionService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InscripcionServiceTest extends TestCase
{
    use RefreshDatabase;

    private InscripcionService $service;

    private Torneo $torneo;

    private Categoria $categoria;

    private Jugador $lider;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Organizador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Jugador', 'guard_name' => 'web']);

        $this->service = new InscripcionService;

        $deporte = Deporte::create(['nombre' => 'Padel', 'slug' => 'padel']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);

        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $complejo = \App\Models\ComplejoDeportivo::create([
            'nombre' => 'Complejo Test',
            'direccion' => 'Calle Falsa 123',
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo = Torneo::create([
            'nombre' => 'Torneo Test',
            'deporte_id' => $deporte->id,
            'complejo_id' => $complejo->id,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'activo',
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $this->categoria = Categoria::create([
            'nombre' => 'Masculino',
            'deporte_id' => $deporte->id,
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo->categorias()->attach($this->categoria->id, [
            'cupos_categoria' => 8,
            'edad_minima' => 18,
            'edad_maxima' => 50,
            'genero_permitido' => 'masculino',
        ]);

        $userLider = User::factory()->create();
        $userLider->assignRole('Jugador');
        $this->lider = Jugador::factory()->create([
            'user_id' => $userLider->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(25),
        ]);
    }

    public function test_iniciar_inscripcion_crea_inscripcion_con_estado_pendiente(): void
    {
        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $this->assertDatabaseHas('inscripciones_equipo', [
            'torneo_id' => $this->torneo->id,
            'categoria_id' => $this->categoria->id,
            'lider_jugador_id' => $this->lider->id,
            'estado' => 'pendiente',
        ]);

        $this->assertNotNull($inscripcion->expires_at);
        $this->assertTrue($inscripcion->expires_at->isFuture());
    }

    public function test_iniciar_inscripcion_crea_invitacion_del_lider_como_aceptada(): void
    {
        $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $this->assertDatabaseHas('invitaciones_jugador', [
            'jugador_id' => $this->lider->id,
            'estado' => 'aceptada',
        ]);
    }

    public function test_no_puede_inscribirse_en_torneo_no_activo(): void
    {
        $this->torneo->update(['estado' => 'borrador']);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
    }

    public function test_no_puede_inscribirse_si_no_cumple_edad(): void
    {
        $userJoven = User::factory()->create();
        $jugadorJoven = Jugador::factory()->create([
            'user_id' => $userJoven->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(16),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($jugadorJoven, $this->torneo, $this->categoria);
    }

    public function test_no_puede_inscribirse_si_no_cumple_genero(): void
    {
        $userFem = User::factory()->create();
        $jugadorFem = Jugador::factory()->create([
            'user_id' => $userFem->id,
            'genero' => 'femenino',
            'fecha_nacimiento' => Carbon::now()->subYears(25),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($jugadorFem, $this->torneo, $this->categoria);
    }

    public function test_no_puede_inscribirse_sin_cupos_disponibles(): void
    {
        $this->torneo->categorias()->updateExistingPivot($this->categoria->id, ['cupos_categoria' => 0]);

        $this->expectException(\RuntimeException::class);
        $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
    }

    public function test_buscar_jugadores_elegibles_filtra_por_condiciones_del_torneo(): void
    {
        $userElegible = User::factory()->create();
        $jugadorElegible = Jugador::factory()->create([
            'user_id' => $userElegible->id,
            'nombre' => 'Carlos',
            'apellido' => 'García',
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(30),
        ]);

        $userNoElegible = User::factory()->create();
        Jugador::factory()->create([
            'user_id' => $userNoElegible->id,
            'nombre' => 'María',
            'apellido' => 'García',
            'genero' => 'femenino',
            'fecha_nacimiento' => Carbon::now()->subYears(30),
        ]);

        $resultado = $this->service->buscarJugadoresElegibles($this->torneo, $this->categoria, 'García');

        $this->assertCount(1, $resultado);
        $this->assertEquals($jugadorElegible->id, $resultado->first()->id);
    }

    public function test_buscar_jugadores_excluye_al_lider(): void
    {
        // Iniciar inscripción registra al lider en invitaciones_jugador con estado aceptada,
        // la búsqueda excluye jugadores con invitaciones en inscripciones pendientes de este torneo/categoría
        $this->lider->update(['apellido' => 'UniqueLiderApellido']);

        $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $resultado = $this->service->buscarJugadoresElegibles($this->torneo, $this->categoria, 'UniqueLiderApellido');

        $ids = $resultado->pluck('id');
        $this->assertNotContains($this->lider->id, $ids);
    }

    public function test_enviar_invitacion_crea_registro_pendiente(): void
    {
        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $userInvitado = User::factory()->create();
        $userInvitado->assignRole('Jugador');
        $jugadorInvitado = Jugador::factory()->create([
            'user_id' => $userInvitado->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(28),
        ]);

        $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);

        $this->assertDatabaseHas('invitaciones_jugador', [
            'inscripcion_equipo_id' => $inscripcion->id,
            'jugador_id' => $jugadorInvitado->id,
            'estado' => 'pendiente',
        ]);

        $this->assertNotEmpty($invitacion->token);
    }

    public function test_debe_auto_aceptar_cuando_flag_activo_y_han_jugado_juntos(): void
    {
        $userInvitado = User::factory()->create();
        $jugadorInvitado = Jugador::factory()->create([
            'user_id' => $userInvitado->id,
            'auto_aceptar_invitaciones' => true,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(28),
        ]);

        // Simular que han jugado juntos en un equipo
        $equipo = \App\Models\Equipo::create([
            'nombre' => 'Equipo anterior',
            'torneo_id' => $this->torneo->id,
            'categoria_id' => $this->categoria->id,
        ]);
        $equipo->jugadores()->attach($this->lider->id, ['orden' => 1]);
        $equipo->jugadores()->attach($jugadorInvitado->id, ['orden' => 2]);

        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
        $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);

        $this->assertEquals('aceptada', $invitacion->fresh()->estado);
        $this->assertTrue($invitacion->fresh()->auto_aceptada);
    }

    public function test_no_auto_acepta_si_nunca_han_jugado_juntos(): void
    {
        $userInvitado = User::factory()->create();
        $jugadorInvitado = Jugador::factory()->create([
            'user_id' => $userInvitado->id,
            'auto_aceptar_invitaciones' => true,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(28),
        ]);

        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);
        $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);

        $this->assertEquals('pendiente', $invitacion->fresh()->estado);
    }

    public function test_verificar_y_confirmar_crea_equipo_cuando_todos_aceptan(): void
    {
        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $userInvitado = User::factory()->create();
        $jugadorInvitado = Jugador::factory()->create([
            'user_id' => $userInvitado->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(28),
        ]);

        $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);
        $invitacion->update(['estado' => 'aceptada', 'respondido_at' => now()]);

        $this->service->verificarYConfirmar($inscripcion->fresh());

        $inscripcion->refresh();
        $this->assertEquals('confirmada', $inscripcion->estado);
        $this->assertNotNull($inscripcion->equipo_id);

        $this->assertDatabaseHas('equipos', [
            'torneo_id' => $this->torneo->id,
            'categoria_id' => $this->categoria->id,
        ]);
    }

    public function test_cancelar_inscripcion_actualiza_estado(): void
    {
        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $this->service->cancelarInscripcion($inscripcion, 'jugador');

        $inscripcion->refresh();
        $this->assertEquals('cancelada', $inscripcion->estado);
        $this->assertEquals('jugador', $inscripcion->cancelado_por);
    }

    public function test_responder_invitacion_rechazar_cancela_inscripcion(): void
    {
        $inscripcion = $this->service->iniciarInscripcion($this->lider, $this->torneo, $this->categoria);

        $userInvitado = User::factory()->create();
        $jugadorInvitado = Jugador::factory()->create([
            'user_id' => $userInvitado->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(28),
        ]);

        $invitacion = $this->service->enviarInvitacion($inscripcion, $jugadorInvitado);
        $this->service->responderInvitacion($invitacion, false);

        $this->assertEquals('rechazada', $invitacion->fresh()->estado);
        $this->assertEquals('cancelada', $inscripcion->fresh()->estado);
    }
}
