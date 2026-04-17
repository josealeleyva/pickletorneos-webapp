<?php

namespace App\Notifications;

use App\Models\InscripcionEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InscripcionConfirmadaNotification extends Notification implements ShouldQueue
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
        $equipo = $this->inscripcion->equipo;
        $url = url('/torneos/'.$torneo->id.'/publico');

        return (new MailMessage)
            ->subject("¡Tu equipo quedó inscripto en {$torneo->nombre}!")
            ->greeting("¡Felicitaciones {$notifiable->name}!")
            ->line("Tu equipo **{$equipo->nombre}** quedó inscripto exitosamente en el torneo **{$torneo->nombre}**.")
            ->line("Categoría: {$this->inscripcion->categoria->nombre}")
            ->action('Ver torneo', $url)
            ->line('El organizador te contactará con los detalles del torneo.');
    }
}
