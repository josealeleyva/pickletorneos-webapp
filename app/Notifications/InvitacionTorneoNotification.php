<?php

namespace App\Notifications;

use App\Models\InvitacionJugador;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitacionTorneoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public InvitacionJugador $invitacion) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inscripcion = $this->invitacion->inscripcionEquipo;
        $lider = $inscripcion->lider;
        $torneo = $inscripcion->torneo;
        $url = url('/inscripciones/invitacion/'.$this->invitacion->token);

        return (new MailMessage)
            ->subject("¡{$lider->nombre_completo} te invita al torneo {$torneo->nombre}!")
            ->greeting("Hola {$notifiable->name},")
            ->line("{$lider->nombre_completo} te invitó a formar equipo en el torneo **{$torneo->nombre}**.")
            ->line("Categoría: {$inscripcion->categoria->nombre}")
            ->line("Complejo: {$torneo->complejo->nombre}")
            ->action('Ver invitación', $url)
            ->line('La invitación expira en 10 minutos. Si el tiempo vence pero hay cupos disponibles, igual se confirmará.');
    }
}
