<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NuevoReferidoNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public User $referido;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $referido)
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
            ->subject('¡Nuevo organizador referido!')
            ->view('emails.nuevo_referido', [
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
            'referido_nombre' => $this->referido->name . ' ' . $this->referido->apellido,
            'referido_email' => $this->referido->email,
        ];
    }
}
