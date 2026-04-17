<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Mostrar el dashboard del organizador
     */
    public function index()
    {
        $user = auth()->user();

        $torneosBorrador = $user->torneos()->where('estado', 'borrador')->count();
        $torneosActivos = $user->torneos()->whereIn('estado', ['activo', 'en_curso'])->count();
        $torneosFinalizados = $user->torneos()->where('estado', 'finalizado')->count();

        return view('dashboard.index', compact('torneosBorrador', 'torneosActivos', 'torneosFinalizados'));
    }
}
