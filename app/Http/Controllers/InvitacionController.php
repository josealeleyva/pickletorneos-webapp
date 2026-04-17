<?php

namespace App\Http\Controllers;

use App\Models\InvitacionJugador;
use App\Services\InscripcionService;
use Illuminate\Support\Facades\Auth;

class InvitacionController extends Controller
{
    public function __construct(private InscripcionService $inscripcionService) {}

    public function mostrar(string $token)
    {
        $invitacion = InvitacionJugador::where('token', $token)
            ->with(['inscripcionEquipo.torneo.complejo', 'inscripcionEquipo.categoria', 'inscripcionEquipo.lider', 'inscripcionEquipo.invitaciones.jugador'])
            ->firstOrFail();

        if (! Auth::check()) {
            session(['invitacion_token_pendiente' => $token]);

            return redirect()->route('login')->with('info', 'Iniciá sesión para responder la invitación.');
        }

        $jugador = Auth::user()->jugador;

        if (! $jugador || $invitacion->jugador_id !== $jugador->id) {
            abort(403, 'Esta invitación no te pertenece.');
        }

        return view('inscripciones.invitacion', compact('invitacion'));
    }

    public function aceptar(string $token)
    {
        $invitacion = InvitacionJugador::where('token', $token)
            ->with('inscripcionEquipo.torneo')
            ->firstOrFail();

        $this->autorizarInvitado($invitacion);

        try {
            $this->inscripcionService->responderInvitacion($invitacion, true);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $torneo = $invitacion->inscripcionEquipo->torneo;

        return redirect()->route('torneos.public', $torneo->id)
            ->with('success', '¡Aceptaste la invitación! Cuando todos confirmen, el equipo quedará inscripto.');
    }

    public function rechazar(string $token)
    {
        $invitacion = InvitacionJugador::where('token', $token)->firstOrFail();

        $this->autorizarInvitado($invitacion);

        try {
            $this->inscripcionService->responderInvitacion($invitacion, false);
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('inscripciones')
            ->with('info', 'Rechazaste la invitación. La inscripción fue cancelada.');
    }

    private function autorizarInvitado(InvitacionJugador $invitacion): void
    {
        $jugador = Auth::user()?->jugador;

        if (! $jugador || $invitacion->jugador_id !== $jugador->id) {
            abort(403);
        }
    }
}
