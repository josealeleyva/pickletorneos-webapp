<?php

namespace App\Jobs;

use App\Models\Referido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpirarReferidosPendientes implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Expirar referidos que llevan más de 6 meses pendientes
        $referidosExpirados = Referido::where('estado', 'pendiente')
            ->where('fecha_registro', '<', now()->subMonths(6))
            ->update(['estado' => 'expirado']);

        if ($referidosExpirados > 0) {
            Log::info("Se expiraron {$referidosExpirados} referidos pendientes (más de 6 meses sin activar)");
        }
    }
}
