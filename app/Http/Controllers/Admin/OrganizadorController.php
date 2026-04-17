<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSistema;
use App\Models\CreditoReferido;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizadorController extends Controller
{
    public function index(Request $request)
    {
        $query = User::role(Roles::Organizador->value())
            ->withCount(['torneos', 'pagosTorneos'])
            ->with(['pagosTorneos' => function ($q) {
                $q->where('estado', 'pagado');
            }]);

        // Filtro de búsqueda
        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->where(function ($q) use ($buscar) {
                $q->where('name', 'like', "%{$buscar}%")
                    ->orWhere('apellido', 'like', "%{$buscar}%")
                    ->orWhere('email', 'like', "%{$buscar}%");
            });
        }

        // Filtro de estado
        if ($request->filled('estado')) {
            if ($request->estado === 'activo') {
                $query->where('cuenta_activa', true);
            } elseif ($request->estado === 'inactivo') {
                $query->where('cuenta_activa', false);
            }
        }

        $organizadores = $query->latest()->paginate(15);

        // Calcular total pagado por cada organizador
        $organizadores->getCollection()->transform(function ($organizador) {
            $organizador->total_pagado = $organizador->pagosTorneos->sum('monto');

            return $organizador;
        });

        return view('admin.organizadores.index', compact('organizadores'));
    }

    public function show(User $user)
    {
        // Verificar que sea organizador
        if (! $user->hasRole(Roles::Organizador->value())) {
            abort(404);
        }

        $user->load([
            'torneos.deporte',
            'pagosTorneos.torneo',
            'referidos', // Ya retorna los usuarios referidos directamente
            'creditosReferidos' => function ($q) {
                $q->where('estado', 'disponible');
            },
        ]);

        $totalPagado = $user->pagosTorneos()->where('estado', 'pagado')->sum('monto');
        $totalTorneos = $user->torneos()->count();
        $referidosActivos = $user->total_referidos_activos ?? 0;
        $creditosDisponibles = $user->creditosReferidos->sum('monto');

        return view('admin.organizadores.show', compact(
            'user',
            'totalPagado',
            'totalTorneos',
            'referidosActivos',
            'creditosDisponibles'
        ));
    }

    public function toggleEstado(Request $request, User $user)
    {
        // Verificar que sea organizador
        if (! $user->hasRole(Roles::Organizador->value())) {
            abort(404);
        }

        $user->cuenta_activa = ! $user->cuenta_activa;
        $user->save();

        $estado = $user->cuenta_activa ? 'activado' : 'desactivado';

        return redirect()->back()->with('success', "Organizador {$estado} exitosamente.");
    }

    public function otorgarCredito(Request $request, User $user)
    {
        // Verificar que sea organizador
        if (! $user->hasRole(Roles::Organizador->value())) {
            abort(404);
        }

        $request->validate([
            'monto' => 'nullable|numeric|min:0',
            'motivo' => 'required|string|max:255',
        ]);

        // Obtener monto del sistema o usar el proporcionado
        $monto = $request->monto ?? ConfiguracionSistema::get('precio_torneo', 25000);

        // Crear crédito manual
        CreditoReferido::create([
            'user_id' => $user->id,
            'referido_id' => null, // Crédito manual, no viene de referido
            'monto' => $monto,
            'estado' => 'disponible',
            'fecha_acreditacion' => now(),
            'fecha_vencimiento' => now()->addMonths(12),
            'notas' => 'Crédito otorgado por administrador: '.$request->motivo,
        ]);

        return redirect()->back()->with('success', "Crédito de \${$monto} otorgado exitosamente.");
    }
}
