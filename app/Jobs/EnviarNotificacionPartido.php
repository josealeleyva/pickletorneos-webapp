<?php

namespace App\Jobs;

use App\Mail\PartidoNotificacionMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionPartido implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected array $jugador;
    protected array $partido;

    public function __construct(array $jugador, array $partido)
    {
        $this->jugador = $jugador;
        $this->partido = $partido;
    }

    public function handle(): void
    {
        if (empty($this->jugador['email'])) {
            return;
        }

        Mail::to($this->jugador['email'])
            ->send(new PartidoNotificacionMail($this->jugador, $this->partido));
    }
}
