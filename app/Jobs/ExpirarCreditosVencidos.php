<?php

namespace App\Jobs;

use App\Models\CreditoReferido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpirarCreditosVencidos implements ShouldQueue
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
        $creditosExpirados = CreditoReferido::where('estado', 'disponible')
            ->where('fecha_vencimiento', '<', now())
            ->update(['estado' => 'expirado']);

        if ($creditosExpirados > 0) {
            Log::info("Se expiraron {$creditosExpirados} créditos de referidos vencidos");
        }
    }
}
