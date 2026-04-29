<?php

namespace Tests\Feature;

use App\Models\Jugador;
use App\Models\User;
use App\Services\DuprService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DuprControllerTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    private Jugador $jugador;

    protected function setUp(): void
    {
        parent::setUp();

        Role::firstOrCreate(['name' => 'Jugador', 'guard_name' => 'web']);

        $this->user = User::factory()->create();
        $this->user->assignRole('Jugador');

        $this->jugador = Jugador::factory()->create(['user_id' => $this->user->id]);
    }

    public function test_buscar_requires_auth(): void
    {
        $response = $this->getJson('/dupr/buscar?q=test');

        $response->assertStatus(401);
    }

    public function test_buscar_returns_jugadores_from_dupr(): void
    {
        $mock = Mockery::mock(DuprService::class);
        $mock->shouldReceive('buscarJugadores')
            ->once()
            ->with('Juan')
            ->andReturn([
                ['duprId' => '1234567890', 'fullName' => 'Juan Perez', 'ratings' => []],
            ]);
        $this->app->instance(DuprService::class, $mock);

        $response = $this->actingAs($this->user)
            ->getJson('/dupr/buscar?q=Juan');

        $response->assertOk()
            ->assertJsonCount(1, 'hits');
    }

    public function test_vincular_saves_dupr_id_and_ratings(): void
    {
        $mock = Mockery::mock(DuprService::class);
        $mock->shouldReceive('obtenerRatingJugador')
            ->once()
            ->with('1234567890')
            ->andReturn(['singles' => 3.5, 'doubles' => 3.8]);
        $this->app->instance(DuprService::class, $mock);

        $response = $this->actingAs($this->user)
            ->post('/dupr/vincular', [
                'dupr_id' => '1234567890',
                'dupr_nombre' => 'Juan Perez',
            ]);

        $response->assertRedirect();

        $this->jugador->refresh();
        $this->assertEquals('1234567890', $this->jugador->dupr_id);
        $this->assertEquals(3.5, $this->jugador->rating_singles);
        $this->assertEquals(3.8, $this->jugador->rating_doubles);
    }

    public function test_vincular_requires_dupr_id(): void
    {
        $response = $this->actingAs($this->user)
            ->post('/dupr/vincular', []);

        $response->assertSessionHasErrors('dupr_id');
    }

    public function test_desconectar_clears_dupr_fields(): void
    {
        $this->jugador->update([
            'dupr_id' => '1234567890',
            'rating_singles' => 3.5,
            'rating_doubles' => 3.8,
        ]);

        $response = $this->actingAs($this->user)
            ->post('/dupr/desconectar');

        $response->assertRedirect();

        $this->jugador->refresh();
        $this->assertNull($this->jugador->dupr_id);
        $this->assertNull($this->jugador->rating_singles);
        $this->assertNull($this->jugador->rating_doubles);
    }

    public function test_desconectar_requires_auth(): void
    {
        $response = $this->post('/dupr/desconectar');

        $response->assertRedirect('/login');
    }
}
