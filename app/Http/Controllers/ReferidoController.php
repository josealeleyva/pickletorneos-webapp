<?php

namespace App\Http\Controllers;

use App\Models\Referido;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReferidoController extends Controller
{
    /**
     * Dashboard de referidos del usuario
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Generar código si no tiene
        if (!$user->codigo_referido) {
            $user->generarCodigoReferido();
        }

        $referidos = Referido::where('referidor_id', $user->id)
            ->with(['referido.torneos'])
            ->latest()
            ->get();

        $creditos = $user->creditosReferidos()
            ->with('referido')
            ->latest()
            ->get();

        $stats = [
            'codigo' => $user->codigo_referido,
            'total_referidos' => $referidos->count(),
            'referidos_activos' => $referidos->where('estado', 'activo')->count(),
            'referidos_pendientes' => $referidos->where('estado', 'pendiente')->count(),
            'creditos_disponibles' => $user->cantidad_creditos,
            'saldo_creditos' => $user->saldo_creditos,
        ];

        return view('referidos.dashboard', compact('referidos', 'creditos', 'stats'));
    }

    /**
     * Página de invitación con código de referido
     */
    public function invitacion($codigo)
    {
        $referidor = User::where('codigo_referido', strtoupper($codigo))->firstOrFail();

        return view('referidos.invitacion', compact('referidor', 'codigo'));
    }
}
