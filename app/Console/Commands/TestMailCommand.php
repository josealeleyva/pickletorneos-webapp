<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {email : Dirección de email destino}';

    protected $description = 'Envía un email de prueba para verificar la configuración de mail';

    public function handle(): int
    {
        $email = $this->argument('email');

        $this->info("Enviando email de prueba a: {$email}");

        Mail::raw(
            "Este es un email de prueba de PickleTorneos.\n\n"
            . "Si recibís este mensaje, la configuración de mail está funcionando correctamente.\n\n"
            . "Fecha/hora: " . now()->format('d/m/Y H:i:s') . "\n"
            . "Driver: " . config('mail.default') . "\n"
            . "Queue: " . config('queue.default'),
            function ($message) use ($email) {
                $message->to($email)
                    ->subject('[PickleTorneos] Email de prueba');
            }
        );

        $this->info('Email enviado. Revisá tu bandeja de entrada (y spam).');

        return self::SUCCESS;
    }
}
