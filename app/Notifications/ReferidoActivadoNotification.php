<?php

namespace App\Notifications;

use App\Models\Referido;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReferidoActivadoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public $referido;

    /**
     * Create a new notification instance.
     */
    public function __construct(Referido $referido)
    {
        $this->referido = $referido;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Ganaste un torneo gratis! 🎉')
            ->view('emails.referido_activado', [
                'referidor' => $notifiable,
                'referido' => $this->referido,
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'referido_id' => $this->referido->id,
            'referido_nombre' => $this->referido->referido->name,
        ];
    }
}
