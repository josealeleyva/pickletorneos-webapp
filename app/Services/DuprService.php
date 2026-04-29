<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class DuprService
{
    private string $baseUrl;

    private string $clientKey;

    private string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = config('services.dupr.base_url');
        $this->clientKey = config('services.dupr.client_key');
        $this->clientSecret = config('services.dupr.client_secret');
    }

    public function obtenerToken(): string
    {
        return Cache::remember('dupr_server_token', now()->addMinutes(55), function () {
            $credentials = base64_encode("{$this->clientKey}:{$this->clientSecret}");

            $response = Http::withHeaders([
                'x-authorization' => $credentials,
                'accept' => 'application/json',
            ])->post("{$this->baseUrl}/api/auth/v1.0/token");

            return $response->json('token');
        });
    }

    public function buscarJugadores(string $query, int $limit = 10): array
    {
        $token = $this->obtenerToken();

        $response = Http::withToken($token)
            ->withHeaders(['accept' => 'application/json'])
            ->post("{$this->baseUrl}/api/user/v1.0/search", [
                'query' => $query,
                'limit' => $limit,
            ]);

        if ($response->failed()) {
            return [];
        }

        return $response->json('hits', []);
    }

    public function obtenerRatingJugador(string $duprId): ?array
    {
        $token = $this->obtenerToken();

        $response = Http::withToken($token)
            ->withHeaders(['accept' => 'application/json'])
            ->get("{$this->baseUrl}/api/user/v1.0/{$duprId}");

        if ($response->failed()) {
            return null;
        }

        $ratings = $response->json('ratings', []);

        return [
            'singles' => $ratings['singles']['rating'] ?? null,
            'doubles' => $ratings['doubles']['rating'] ?? null,
        ];
    }

    public function buscarPorEmail(string $email): ?array
    {
        $hits = $this->buscarJugadores($email, 5);

        if (empty($hits)) {
            return null;
        }

        return $hits[0];
    }

    public function invitarJugador(string $nombre, string $email): ?string
    {
        $token = $this->obtenerToken();

        $response = Http::withToken($token)
            ->withHeaders(['accept' => 'application/json'])
            ->post("{$this->baseUrl}/api/user/v1.0/invite", [
                'fullName' => $nombre,
                'email' => $email,
            ]);

        if ($response->failed()) {
            return null;
        }

        return $response->json('duprId');
    }

    public function crearPartido(array $payload): ?string
    {
        $token = $this->obtenerToken();

        $response = Http::withToken($token)
            ->withHeaders(['accept' => 'application/json'])
            ->post("{$this->baseUrl}/api/match/v1.0/create", $payload);

        if ($response->failed()) {
            return null;
        }

        return $response->json('matchCode');
    }
}
