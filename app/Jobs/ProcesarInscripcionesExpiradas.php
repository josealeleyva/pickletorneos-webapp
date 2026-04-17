<?php

namespace App\Jobs;

use App\Models\InscripcionEquipo;
use App\Services\InscripcionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcesarInscripcionesExpiradas implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(InscripcionService $service): void
    {
        $inscripcionesExpiradas = InscripcionEquipo::where('estado', 'pendiente')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->with(['torneo.formato', 'torneo.equipos', 'categoria', 'invitaciones.jugador.user'])
            ->get();

        foreach ($inscripcionesExpiradas as $inscripcion) {
            $torneo = $inscripcion->torneo;
            $categoriaConPivot = $torneo->categorias()
                ->where('categorias.id', $inscripcion->categoria_id)
                ->first();

            if (! $categoriaConPivot) {
                $service->cancelarInscripcion($inscripcion, 'expiracion');

                continue;
            }

            $cuposDisponibles = $service->calcularCuposDisponibles($torneo, $categoriaConPivot);

            if ($cuposDisponibles > 0) {
                $service->verificarYConfirmar($inscripcion);

                if ($inscripcion->fresh()->estado !== 'confirmada') {
                    $service->cancelarInscripcion($inscripcion->fresh(), 'expiracion');
                }
            } else {
                $service->cancelarInscripcion($inscripcion, 'expiracion');
            }

            Log::info('Inscripción expirada procesada', ['inscripcion_id' => $inscripcion->id]);
        }
    }
}
