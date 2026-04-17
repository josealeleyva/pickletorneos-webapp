<?php

namespace App\Mail;

use App\Models\Sugerencia;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SugerenciaRespondidaMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sugerencia;

    /**
     * Create a new message instance.
     */
    public function __construct(Sugerencia $sugerencia)
    {
        $this->sugerencia = $sugerencia;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Respuesta a tu '.ucfirst($this->sugerencia->tipo).' - Punto de Oro',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.sugerencia_respondida',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
