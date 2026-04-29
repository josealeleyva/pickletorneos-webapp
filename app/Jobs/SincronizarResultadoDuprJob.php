<?php

namespace App\Jobs;

use App\Models\Partido;
use App\Services\DuprService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SincronizarResultadoDuprJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [60, 300, 900];

    public function __construct(public int $partidoId) {}

    public function handle(DuprService $duprService): void
    {
        $partido = Partido::with([
            'equipo1.jugadores' => fn ($q) => $q->orderByPivot('orden'),
            'equipo2.jugadores' => fn ($q) => $q->orderByPivot('orden'),
            'juegos' => fn ($q) => $q->orderBy('orden'),
        ])->findOrFail($this->partidoId);

        if ($partido->dupr_sincronizado) {
            return;
        }

        $jugadoresEquipo1 = $partido->equipo1->jugadores;
        $jugadoresEquipo2 = $partido->equipo2->jugadores;

        $duprIds = array_filter(array_merge(
            $jugadoresEquipo1->pluck('dupr_id')->toArray(),
            $jugadoresEquipo2->pluck('dupr_id')->toArray(),
        ));

        $totalJugadores = $jugadoresEquipo1->count() + $jugadoresEquipo2->count();

        if (count($duprIds) < $totalJugadores) {
            $partido->update(['dupr_error' => 'Uno o más jugadores no tienen DUPR ID vinculado.']);

            return;
        }

        $games = $partido->juegos->map(fn ($juego) => [
            'team1Score' => $juego->juegos_equipo1,
            'team2Score' => $juego->juegos_equipo2,
        ])->values()->toArray();

        $payload = [
            'eventName' => "Partido #{$partido->id}",
            'matchDate' => $partido->updated_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'matchType' => 'DOUBLES',
            'team1' => $jugadoresEquipo1->pluck('dupr_id')->values()->toArray(),
            'team2' => $jugadoresEquipo2->pluck('dupr_id')->values()->toArray(),
            'games' => $games,
        ];

        $matchCode = $duprService->crearPartido($payload);

        if ($matchCode === null) {
            throw new \RuntimeException("DUPR API no retornó matchCode para el partido #{$this->partidoId}");
        }

        $partido->update([
            'dupr_partido_id' => $matchCode,
            'dupr_sincronizado' => true,
            'dupr_sincronizado_at' => now(),
            'dupr_error' => null,
        ]);
    }

    public function failed(\Throwable $exception): void
    {
        Partido::where('id', $this->partidoId)->update([
            'dupr_error' => $exception->getMessage(),
        ]);
    }
}
