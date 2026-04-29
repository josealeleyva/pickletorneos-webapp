<?php

namespace Tests\Feature;

use App\Services\DuprService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DuprServiceTest extends TestCase
{
    private DuprService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = new DuprService;
    }

    public function test_obtener_token_calls_dupr_auth_endpoint(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'abc123'], 200),
        ]);

        $token = $this->service->obtenerToken();

        $this->assertEquals('abc123', $token);
        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/auth/v1.0/token')
                && $request->hasHeader('x-authorization');
        });
    }

    public function test_obtener_token_uses_base64_encoded_key_secret(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'xyz'], 200),
        ]);

        $this->service->obtenerToken();

        Http::assertSent(function ($request) {
            $expected = base64_encode(config('services.dupr.client_key').':'.config('services.dupr.client_secret'));

            return $request->header('x-authorization')[0] === $expected;
        });
    }

    public function test_obtener_token_caches_result(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::sequence()
                ->push(['token' => 'first_token'], 200)
                ->push(['token' => 'second_token'], 200),
        ]);

        $first = $this->service->obtenerToken();
        $second = $this->service->obtenerToken();

        $this->assertEquals('first_token', $first);
        $this->assertEquals('first_token', $second);
        Http::assertSentCount(1);
    }

    public function test_buscar_jugadores_returns_hits_array(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'tok'], 200),
            '*/api/user/v1.0/search' => Http::response([
                'hits' => [
                    ['duprId' => '1234567890', 'fullName' => 'Juan Perez', 'ratings' => ['singles' => ['rating' => 3.5], 'doubles' => ['rating' => 3.8]]],
                ],
            ], 200),
        ]);

        $results = $this->service->buscarJugadores('Juan Perez');

        $this->assertCount(1, $results);
        $this->assertEquals('1234567890', $results[0]['duprId']);
        $this->assertEquals('Juan Perez', $results[0]['fullName']);
    }

    public function test_buscar_jugadores_sends_bearer_token(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'bearer_tok'], 200),
            '*/api/user/v1.0/search' => Http::response(['hits' => []], 200),
        ]);

        $this->service->buscarJugadores('test');

        Http::assertSent(function ($request) {
            return str_contains($request->url(), '/api/user/v1.0/search')
                && $request->header('Authorization')[0] === 'Bearer bearer_tok';
        });
    }

    public function test_obtener_rating_jugador_returns_singles_and_doubles(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'tok'], 200),
            '*/api/user/v1.0/1234567890' => Http::response([
                'ratings' => [
                    'singles' => ['rating' => 4.12],
                    'doubles' => ['rating' => 3.89],
                ],
            ], 200),
        ]);

        $rating = $this->service->obtenerRatingJugador('1234567890');

        $this->assertEquals(4.12, $rating['singles']);
        $this->assertEquals(3.89, $rating['doubles']);
    }

    public function test_obtener_rating_jugador_returns_null_on_404(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'tok'], 200),
            '*/api/user/v1.0/*' => Http::response([], 404),
        ]);

        $rating = $this->service->obtenerRatingJugador('0000000000');

        $this->assertNull($rating);
    }

    public function test_crear_partido_returns_match_code(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'tok'], 200),
            '*/api/match/v1.0/create' => Http::response(['matchCode' => 'MC-001'], 201),
        ]);

        $payload = [
            'eventName' => 'Torneo Test',
            'matchDate' => '2026-04-27',
            'players' => [],
        ];

        $matchCode = $this->service->crearPartido($payload);

        $this->assertEquals('MC-001', $matchCode);
    }

    public function test_crear_partido_returns_null_on_error(): void
    {
        Http::fake([
            '*/api/auth/v1.0/token' => Http::response(['token' => 'tok'], 200),
            '*/api/match/v1.0/create' => Http::response(['error' => 'bad request'], 400),
        ]);

        $matchCode = $this->service->crearPartido([]);

        $this->assertNull($matchCode);
    }
}
