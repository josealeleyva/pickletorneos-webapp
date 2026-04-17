<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Expirar créditos de referidos vencidos (diario a las 2 AM)
        $schedule->job(new \App\Jobs\ExpirarCreditosVencidos)
            ->daily()
            ->at('02:00')
            ->name('expirar-creditos-vencidos')
            ->onOneServer();

        // Expirar referidos pendientes después de 6 meses (diario a las 3 AM)
        $schedule->job(new \App\Jobs\ExpirarReferidosPendientes)
            ->daily()
            ->at('03:00')
            ->name('expirar-referidos-pendientes')
            ->onOneServer();

        // Procesar inscripciones expiradas (cada 5 minutos)
        $schedule->job(new \App\Jobs\ProcesarInscripcionesExpiradas)
            ->everyFiveMinutes()
            ->name('procesar-inscripciones-expiradas')
            ->onOneServer();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
