<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\SugerenciaRespondidaMail;
use App\Models\Sugerencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SugerenciaController extends Controller
{
    public function index(Request $request)
    {
        $query = Sugerencia::with(['user', 'respondidaPor']);

        // Filtro de estado
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        } else {
            // Por defecto mostrar pendientes
            $query->pendientes();
        }

        // Filtro de tipo
        if ($request->filled('tipo')) {
            $query->where('tipo', $request->tipo);
        }

        $sugerencias = $query->latest()->paginate(15);

        $totalNuevas = Sugerencia::nuevas()->count();
        $totalEnRevision = Sugerencia::enRevision()->count();
        $totalPendientes = Sugerencia::pendientes()->count();
        $totalRespondidas = Sugerencia::where('estado', 'respondida')->count();

        // Distribución por tipo
        $distribucionTipo = Sugerencia::select('tipo', \DB::raw('count(*) as total'))
            ->groupBy('tipo')
            ->get();

        return view('admin.sugerencias.index', compact(
            'sugerencias',
            'totalNuevas',
            'totalEnRevision',
            'totalPendientes',
            'totalRespondidas',
            'distribucionTipo'
        ));
    }

    public function show(Sugerencia $sugerencia)
    {
        $sugerencia->load(['user', 'respondidaPor']);

        // Marcar como en revisión si está nueva
        if ($sugerencia->estado === 'nueva') {
            $sugerencia->update(['estado' => 'en_revision']);
        }

        return view('admin.sugerencias.show', compact('sugerencia'));
    }

    public function responder(Request $request, Sugerencia $sugerencia)
    {
        $request->validate([
            'respuesta' => 'required|string|min:10',
        ]);

        $sugerencia->update([
            'respuesta' => $request->respuesta,
            'estado' => 'respondida',
            'respondida_en' => now(),
            'respondida_por' => auth()->id(),
        ]);

        // Enviar email al usuario
        Mail::to($sugerencia->user->email)->send(new SugerenciaRespondidaMail($sugerencia));

        return redirect()->back()->with('success', 'Respuesta enviada exitosamente.');
    }

    public function cambiarEstado(Request $request, Sugerencia $sugerencia)
    {
        $request->validate([
            'estado' => 'required|in:nueva,en_revision,respondida,cerrada',
        ]);

        $sugerencia->update(['estado' => $request->estado]);

        return redirect()->back()->with('success', 'Estado actualizado exitosamente.');
    }
}
