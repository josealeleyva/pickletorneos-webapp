<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\InscripcionEquipo;
use App\Models\InvitacionJugador;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvitacionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $userLider;

    private User $userInvitado;

    private Jugador $jugadorLider;

    private Jugador $jugadorInvitado;

    private InscripcionEquipo $inscripcion;

    private InvitacionJugador $invitacion;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Organizador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Jugador', 'guard_name' => 'web']);

        $deporte = Deporte::create(['nombre' => 'Padel', 'slug' => 'padel']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);
        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $complejo = ComplejoDeportivo::create([
            'nombre' => 'Complejo Test',
            'direccion' => 'Calle Falsa 123',
            'organizador_id' => $organizador->id,
        ]);

        $torneo = Torneo::create([
            'nombre' => 'Torneo Test',
            'deporte_id' => $deporte->id,
            'complejo_id' => $complejo->id,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'activo',
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
        ]);

        $categoria = Categoria::create([
            'nombre' => 'Masculino',
            'deporte_id' => $deporte->id,
            'organizador_id' => $organizador->id,
        ]);

        $torneo->categorias()->attach($categoria->id, ['cupos_categoria' => 8]);

        $this->userLider = User::factory()->create();
        $this->userLider->assignRole('Jugador');
        $this->jugadorLider = Jugador::factory()->create(['user_id' => $this->userLider->id]);

        $this->userInvitado = User::factory()->create();
        $this->userInvitado->assignRole('Jugador');
        $this->jugadorInvitado = Jugador::factory()->create(['user_id' => $this->userInvitado->id]);

        $this->inscripcion = InscripcionEquipo::create([
            'torneo_id' => $torneo->id,
            'categoria_id' => $categoria->id,
            'lider_jugador_id' => $this->jugadorLider->id,
            'estado' => 'pendiente',
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->invitacion = InvitacionJugador::create([
            'inscripcion_equipo_id' => $this->inscripcion->id,
            'jugador_id' => $this->jugadorInvitado->id,
            'estado' => 'pendiente',
            'token' => Str::random(40),
        ]);
    }

    public function test_jugador_puede_ver_su_invitacion(): void
    {
        $response = $this->actingAs($this->userInvitado)
            ->get(route('inscripciones.invitacion.mostrar', $this->invitacion->token));

        $response->assertOk();
    }

    public function test_jugador_puede_aceptar_invitacion(): void
    {
        // Crear también la invitación del líder como aceptada para que "todos acepten"
        InvitacionJugador::create([
            'inscripcion_equipo_id' => $this->inscripcion->id,
            'jugador_id' => $this->jugadorLider->id,
            'estado' => 'aceptada',
            'token' => Str::random(40),
            'respondido_at' => now(),
        ]);

        $response = $this->actingAs($this->userInvitado)
            ->post(route('inscripciones.invitacion.aceptar', $this->invitacion->token));

        $response->assertRedirect();
        $this->assertEquals('aceptada', $this->invitacion->fresh()->estado);
    }

    public function test_jugador_puede_rechazar_invitacion(): void
    {
        $response = $this->actingAs($this->userInvitado)
            ->post(route('inscripciones.invitacion.rechazar', $this->invitacion->token));

        $response->assertRedirect();
        $this->assertEquals('rechazada', $this->invitacion->fresh()->estado);
        $this->assertEquals('cancelada', $this->inscripcion->fresh()->estado);
    }

    public function test_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        $response = $this->get(route('inscripciones.invitacion.mostrar', $this->invitacion->token));

        $response->assertRedirect('/login');
    }

    public function test_otro_jugador_no_puede_responder_invitacion_ajena(): void
    {
        $otroUser = User::factory()->create();

        $response = $this->actingAs($otroUser)
            ->post(route('inscripciones.invitacion.aceptar', $this->invitacion->token));

        $response->assertForbidden();
    }
}
