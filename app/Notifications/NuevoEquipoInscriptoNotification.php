<?php

namespace App\Notifications;

use App\Models\InscripcionEquipo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoEquipoInscriptoNotification extends Notification implements ShouldQueue
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
        $url = url('/torneos/'.$torneo->id.'/equipos');

        return (new MailMessage)
            ->subject("Nuevo equipo inscripto en {$torneo->nombre}")
            ->greeting("Hola {$notifiable->name},")
            ->line("El equipo **{$equipo->nombre}** se inscribió en tu torneo **{$torneo->nombre}**.")
            ->line("Categoría: {$this->inscripcion->categoria->nombre}")
            ->action('Ver equipos', $url);
    }
}
