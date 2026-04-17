<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\InscripcionEquipo;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InscripcionControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $userJugador;

    private Jugador $jugador;

    private Torneo $torneo;

    private Categoria $categoria;

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
            'edad_minima' => null,
            'edad_maxima' => null,
            'genero_permitido' => null,
        ]);

        $this->userJugador = User::factory()->create();
        $this->userJugador->assignRole('Jugador');
        $this->jugador = Jugador::factory()->create([
            'user_id' => $this->userJugador->id,
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(25),
        ]);
    }

    public function test_jugador_puede_ver_formulario_de_inscripcion(): void
    {
        $response = $this->actingAs($this->userJugador)
            ->get(route('torneos.inscripciones.crear', $this->torneo));

        $response->assertOk();
    }

    public function test_jugador_puede_iniciar_inscripcion(): void
    {
        $response = $this->actingAs($this->userJugador)
            ->post(route('torneos.inscripciones.store', $this->torneo), [
                'categoria_id' => $this->categoria->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('inscripciones_equipo', [
            'torneo_id' => $this->torneo->id,
            'lider_jugador_id' => $this->jugador->id,
            'estado' => 'pendiente',
        ]);
    }

    public function test_usuario_sin_perfil_jugador_no_puede_inscribirse(): void
    {
        $userSinJugador = User::factory()->create();

        $response = $this->actingAs($userSinJugador)
            ->post(route('torneos.inscripciones.store', $this->torneo), [
                'categoria_id' => $this->categoria->id,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseEmpty('inscripciones_equipo');
    }

    public function test_buscar_jugadores_devuelve_json(): void
    {
        $userElegible = User::factory()->create();
        Jugador::factory()->create([
            'user_id' => $userElegible->id,
            'nombre' => 'Carlos',
            'apellido' => 'García',
            'genero' => 'masculino',
            'fecha_nacimiento' => Carbon::now()->subYears(28),
        ]);

        $response = $this->actingAs($this->userJugador)
            ->get(route('torneos.inscripciones.buscar', [
                'torneo' => $this->torneo->id,
                'categoria_id' => $this->categoria->id,
                'q' => 'García',
            ]));

        $response->assertOk();
        $response->assertJsonCount(1);
    }

    public function test_lider_puede_cancelar_inscripcion(): void
    {
        $inscripcion = InscripcionEquipo::create([
            'torneo_id' => $this->torneo->id,
            'categoria_id' => $this->categoria->id,
            'lider_jugador_id' => $this->jugador->id,
            'estado' => 'pendiente',
            'expires_at' => now()->addMinutes(10),
        ]);

        $response = $this->actingAs($this->userJugador)
            ->delete(route('inscripciones.cancelar', $inscripcion));

        $response->assertRedirect();
        $this->assertEquals('cancelada', $inscripcion->fresh()->estado);
    }
}
