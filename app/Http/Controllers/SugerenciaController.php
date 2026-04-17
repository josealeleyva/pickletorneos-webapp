<?php

namespace App\Http\Controllers;

use App\Models\Sugerencia;
use Illuminate\Http\Request;

class SugerenciaController extends Controller
{
    public function index()
    {
        $sugerencias = Sugerencia::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('sugerencias.index', compact('sugerencias'));
    }

    public function create()
    {
        return view('sugerencias.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipo' => 'required|in:sugerencia,soporte,bug,otro',
            'asunto' => 'required|string|max:255',
            'mensaje' => 'required|string|min:10',
        ]);

        Sugerencia::create([
            'user_id' => auth()->id(),
            'tipo' => $request->tipo,
            'asunto' => $request->asunto,
            'mensaje' => $request->mensaje,
            'estado' => 'nueva',
        ]);

        return redirect()->route('sugerencias.index')
            ->with('success', 'Tu sugerencia ha sido enviada. Te notificaremos cuando sea respondida.');
    }

    public function show(Sugerencia $sugerencia)
    {
        // Verificar que la sugerencia pertenezca al usuario
        if ($sugerencia->user_id !== auth()->id()) {
            abort(403);
        }

        return view('sugerencias.show', compact('sugerencia'));
    }
}
