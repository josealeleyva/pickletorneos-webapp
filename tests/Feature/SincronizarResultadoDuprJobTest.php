<?php

namespace Tests\Feature;

use App\Jobs\SincronizarResultadoDuprJob;
use App\Models\Equipo;
use App\Models\Juego;
use App\Models\Jugador;
use App\Models\Partido;
use App\Models\Torneo;
use App\Services\DuprService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SincronizarResultadoDuprJobTest extends TestCase
{
    use RefreshDatabase;

    private function makePartidoConDupr(): Partido
    {
        $torneo = Torneo::factory()->create(['dupr_requerido' => true]);

        $jugador1 = Jugador::factory()->create(['dupr_id' => '1111111111']);
        $jugador2 = Jugador::factory()->create(['dupr_id' => '2222222222']);
        $jugador3 = Jugador::factory()->create(['dupr_id' => '3333333333']);
        $jugador4 = Jugador::factory()->create(['dupr_id' => '4444444444']);

        $equipo1 = Equipo::factory()->create(['torneo_id' => $torneo->id]);
        $equipo1->jugadores()->attach([$jugador1->id => ['orden' => 1], $jugador2->id => ['orden' => 2]]);

        $equipo2 = Equipo::factory()->create(['torneo_id' => $torneo->id]);
        $equipo2->jugadores()->attach([$jugador3->id => ['orden' => 1], $jugador4->id => ['orden' => 2]]);

        $partido = Partido::factory()->create([
            'equipo1_id' => $equipo1->id,
            'equipo2_id' => $equipo2->id,
            'dupr_sincronizado' => false,
        ]);

        Juego::factory()->create([
            'partido_id' => $partido->id,
            'juegos_equipo1' => 11,
            'juegos_equipo2' => 7,
            'orden' => 1,
        ]);

        return $partido;
    }

    public function test_job_creates_partido_in_dupr_and_marks_sincronizado(): void
    {
        $partido = $this->makePartidoConDupr();

        $mock = Mockery::mock(DuprService::class);
        $mock->shouldReceive('obtenerToken')->andReturn('tok');
        $mock->shouldReceive('crearPartido')->once()->andReturn('MC-TEST-001');
        $this->app->instance(DuprService::class, $mock);

        (new SincronizarResultadoDuprJob($partido->id))->handle($mock);

        $partido->refresh();
        $this->assertTrue($partido->dupr_sincronizado);
        $this->assertEquals('MC-TEST-001', $partido->dupr_partido_id);
        $this->assertNotNull($partido->dupr_sincronizado_at);
    }

    public function test_job_skips_if_already_sincronizado(): void
    {
        $partido = $this->makePartidoConDupr();
        $partido->update(['dupr_sincronizado' => true, 'dupr_partido_id' => 'EXISTING']);

        $mock = Mockery::mock(DuprService::class);
        $mock->shouldNotReceive('crearPartido');
        $this->app->instance(DuprService::class, $mock);

        (new SincronizarResultadoDuprJob($partido->id))->handle($mock);

        $this->assertEquals('EXISTING', $partido->fresh()->dupr_partido_id);
    }

    public function test_job_records_error_when_player_lacks_dupr_id(): void
    {
        $torneo = Torneo::factory()->create(['dupr_requerido' => true]);

        $jugadorSinDupr = Jugador::factory()->create(['dupr_id' => null]);
        $jugador2 = Jugador::factory()->create(['dupr_id' => '2222222222']);

        $equipo1 = Equipo::factory()->create(['torneo_id' => $torneo->id]);
        $equipo1->jugadores()->attach([$jugadorSinDupr->id => ['orden' => 1], $jugador2->id => ['orden' => 2]]);

        $equipo2 = Equipo::factory()->create(['torneo_id' => $torneo->id]);
        $jugador3 = Jugador::factory()->create(['dupr_id' => '3333333333']);
        $jugador4 = Jugador::factory()->create(['dupr_id' => '4444444444']);
        $equipo2->jugadores()->attach([$jugador3->id => ['orden' => 1], $jugador4->id => ['orden' => 2]]);

        $partido = Partido::factory()->create([
            'equipo1_id' => $equipo1->id,
            'equipo2_id' => $equipo2->id,
            'dupr_sincronizado' => false,
        ]);

        $mock = Mockery::mock(DuprService::class);
        $mock->shouldNotReceive('crearPartido');
        $this->app->instance(DuprService::class, $mock);

        (new SincronizarResultadoDuprJob($partido->id))->handle($mock);

        $partido->refresh();
        $this->assertFalse($partido->dupr_sincronizado);
        $this->assertNotNull($partido->dupr_error);
    }

    public function test_job_throws_exception_on_api_failure_to_trigger_retry(): void
    {
        $partido = $this->makePartidoConDupr();

        $mock = Mockery::mock(DuprService::class);
        $mock->shouldReceive('obtenerToken')->andReturn('tok');
        $mock->shouldReceive('crearPartido')->once()->andReturn(null);
        $this->app->instance(DuprService::class, $mock);

        $this->expectException(\RuntimeException::class);

        (new SincronizarResultadoDuprJob($partido->id))->handle($mock);
    }
}
