<?php

namespace Tests\Feature;

use App\Models\Categoria;
use App\Models\ComplejoDeportivo;
use App\Models\Deporte;
use App\Models\FormatoTorneo;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use App\Services\InscripcionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InscripcionServiceDuprTest extends TestCase
{
    use RefreshDatabase;

    private InscripcionService $service;

    private Torneo $torneo;

    private Categoria $categoria;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Organizador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Jugador', 'guard_name' => 'web']);

        $this->service = app(InscripcionService::class);

        $deporte = Deporte::create(['nombre' => 'Pickleball', 'slug' => 'pickleball']);
        $formato = FormatoTorneo::create(['nombre' => 'Eliminación Directa', 'slug' => 'eliminacion_directa', 'tiene_grupos' => false]);
        $organizador = User::factory()->create();
        $organizador->assignRole('Organizador');

        $complejo = ComplejoDeportivo::create([
            'nombre' => 'Complejo Test',
            'direccion' => 'Calle Falsa 123',
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo = Torneo::create([
            'nombre' => 'Torneo DUPR Test',
            'deporte_id' => $deporte->id,
            'complejo_id' => $complejo->id,
            'organizador_id' => $organizador->id,
            'formato_id' => $formato->id,
            'estado' => 'activo',
            'fecha_inicio' => now()->addDays(10),
            'fecha_fin' => now()->addDays(12),
            'dupr_requerido' => true,
        ]);

        $this->categoria = Categoria::create([
            'nombre' => 'Open',
            'deporte_id' => $deporte->id,
            'organizador_id' => $organizador->id,
        ]);

        $this->torneo->categorias()->attach($this->categoria->id, [
            'cupos_categoria' => 8,
            'edad_minima' => null,
            'edad_maxima' => null,
            'genero_permitido' => null,
            'dupr_rating_min' => null,
            'dupr_rating_max' => null,
        ]);
    }

    private function getCategoriaConPivot(): Categoria
    {
        return $this->torneo->categorias()->where('categorias.id', $this->categoria->id)->first();
    }

    public function test_validar_throws_when_dupr_requerido_and_jugador_has_no_dupr_id(): void
    {
        $jugador = Jugador::factory()->create(['dupr_id' => null]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('vincular tu cuenta DUPR');

        $this->service->validarCondicionesJugador($jugador, $this->getCategoriaConPivot(), $this->torneo);
    }

    public function test_validar_passes_when_dupr_requerido_and_jugador_has_dupr_id(): void
    {
        $jugador = Jugador::factory()->create(['dupr_id' => '1234567890']);

        $this->service->validarCondicionesJugador($jugador, $this->getCategoriaConPivot(), $this->torneo);

        $this->assertTrue(true);
    }

    public function test_validar_passes_when_torneo_not_dupr_requerido_even_without_dupr_id(): void
    {
        $this->torneo->update(['dupr_requerido' => false]);
        $jugador = Jugador::factory()->create(['dupr_id' => null]);

        $this->service->validarCondicionesJugador($jugador, $this->getCategoriaConPivot(), $this->torneo);

        $this->assertTrue(true);
    }

    public function test_validar_throws_when_rating_below_minimum(): void
    {
        $this->torneo->categorias()->updateExistingPivot($this->categoria->id, [
            'dupr_rating_min' => 4.0,
            'dupr_rating_max' => null,
        ]);

        $jugador = Jugador::factory()->create(['dupr_id' => '1234567890', 'rating_doubles' => 3.5]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('menor al mínimo requerido');

        $this->service->validarCondicionesJugador($jugador, $this->getCategoriaConPivot(), $this->torneo);
    }

    public function test_validar_throws_when_rating_above_maximum(): void
    {
        $this->torneo->categorias()->updateExistingPivot($this->categoria->id, [
            'dupr_rating_min' => null,
            'dupr_rating_max' => 3.5,
        ]);

        $jugador = Jugador::factory()->create(['dupr_id' => '1234567890', 'rating_doubles' => 4.0]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('supera el máximo permitido');

        $this->service->validarCondicionesJugador($jugador, $this->getCategoriaConPivot(), $this->torneo);
    }

    public function test_validar_throws_when_rating_required_but_null(): void
    {
        $this->torneo->categorias()->updateExistingPivot($this->categoria->id, [
            'dupr_rating_min' => 3.0,
        ]);

        $jugador = Jugador::factory()->create(['dupr_id' => '1234567890', 'rating_doubles' => null]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('no tiene rating registrado');

        $this->service->validarCondicionesJugador($jugador, $this->getCategoriaConPivot(), $this->torneo);
    }

    public function test_buscar_elegibles_filters_out_players_without_dupr_id(): void
    {
        $userSinDupr = User::factory()->create();
        $userSinDupr->assignRole('Jugador');
        Jugador::factory()->create(['user_id' => $userSinDupr->id, 'dupr_id' => null, 'nombre' => 'Juan', 'apellido' => 'Perez']);

        $userConDupr = User::factory()->create();
        $userConDupr->assignRole('Jugador');
        Jugador::factory()->create(['user_id' => $userConDupr->id, 'dupr_id' => '1234567890', 'nombre' => 'Juan', 'apellido' => 'Lopez']);

        $result = $this->service->buscarJugadoresElegibles($this->torneo, $this->categoria, 'Juan');

        $this->assertCount(1, $result);
        $this->assertEquals('Lopez', $result->first()->apellido);
    }
}
