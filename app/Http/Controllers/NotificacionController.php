<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class NotificacionController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $notificaciones = $user->notificaciones()
            ->orderByPivot('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'mensaje' => $n->mensaje,
                'tipo' => $n->tipo,
                'leida' => (bool) $n->pivot->leida,
                'fecha' => $n->enviado_at?->diffForHumans() ?? $n->created_at->diffForHumans(),
            ]);

        $noLeidas = $user->notificaciones()
            ->wherePivot('leida', false)
            ->count();

        return response()->json([
            'notificaciones' => $notificaciones,
            'no_leidas' => $noLeidas,
        ]);
    }

    public function marcarLeida(int $id)
    {
        Auth::user()->notificaciones()->updateExistingPivot($id, [
            'leida' => true,
            'leida_at' => now(),
        ]);

        return response()->json(['ok' => true]);
    }

    public function marcarTodasLeidas()
    {
        Auth::user()->notificaciones()
            ->wherePivot('leida', false)
            ->each(fn ($n) => Auth::user()->notificaciones()->updateExistingPivot($n->id, [
                'leida' => true,
                'leida_at' => now(),
            ]));

        return response()->json(['ok' => true]);
    }
}
