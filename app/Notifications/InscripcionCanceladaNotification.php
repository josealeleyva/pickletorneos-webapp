<?php

namespace App\Notifications;

use App\Models\InscripcionEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscripcionCanceladaNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InscripcionEquipo $inscripcion) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $torneo = $this->inscripcion->torneo;

        $motivo = match ($this->inscripcion->cancelado_por) {
            'organizador' => 'el organizador eliminó el equipo del torneo.',
            'jugador' => 'un jugador rechazó la invitación.',
            'expiracion' => 'el tiempo de inscripción venció y no había cupos disponibles.',
            default => 'la inscripción fue cancelada.',
        };

        return (new MailMessage)
            ->subject("Inscripción cancelada — {$torneo->nombre}")
            ->greeting("Hola {$notifiable->name},")
            ->line("Tu inscripción al torneo **{$torneo->nombre}** fue cancelada porque {$motivo}")
            ->line('Podés intentar inscribirte nuevamente si hay cupos disponibles.')
            ->action('Ver torneo', url('/torneos/'.$torneo->id.'/publico'));
    }
}
