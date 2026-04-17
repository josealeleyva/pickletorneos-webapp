<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class PartidoNotificacionMail extends Mailable
{
    public $jugador;
    public $partido;

    public function __construct($jugador, $partido)
    {
        $this->jugador = $jugador;
        $this->partido = $partido;
    }

    public function build()
    {
        return $this->subject('🎾 ¡Tu próximo partido está confirmado!')
                    ->view('emails.partido_notificacion');
    }
}
