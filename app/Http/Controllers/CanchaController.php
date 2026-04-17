<?php

namespace App\Http\Controllers;

use App\Models\Cancha;
use App\Models\ComplejoDeportivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanchaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ComplejoDeportivo $complejo)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('view', $complejo);

        $canchas = $complejo->canchas()->orderBy('numero')->get();

        return view('canchas.index', compact('complejo', 'canchas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(ComplejoDeportivo $complejo)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('view', $complejo);

        return view('canchas.create', compact('complejo'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, ComplejoDeportivo $complejo)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('view', $complejo);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'numero' => 'required|integer|min:1',
        ]);

        $validated['complejo_id'] = $complejo->id;

        $cancha = Cancha::create($validated);

        return redirect()
            ->route('complejos.canchas.index', $complejo)
            ->with('success', '¡Cancha creada exitosamente!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ComplejoDeportivo $complejo, Cancha $cancha)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('view', $complejo);

        // Verificar que la cancha pertenezca al complejo
        if ($cancha->complejo_id !== $complejo->id) {
            abort(404);
        }

        return view('canchas.edit', compact('complejo', 'cancha'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComplejoDeportivo $complejo, Cancha $cancha)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('update', $complejo);

        // Verificar que la cancha pertenezca al complejo
        if ($cancha->complejo_id !== $complejo->id) {
            abort(404);
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'numero' => 'required|integer|min:1',
        ]);

        $cancha->update($validated);

        return redirect()
            ->route('complejos.canchas.index', $complejo)
            ->with('success', '¡Cancha actualizada exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComplejoDeportivo $complejo, Cancha $cancha)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('delete', $complejo);

        // Verificar que la cancha pertenezca al complejo
        if ($cancha->complejo_id !== $complejo->id) {
            abort(404);
        }

        $nombre = $cancha->nombre;
        $cancha->delete();

        return redirect()
            ->route('complejos.canchas.index', $complejo)
            ->with('success', "Cancha '{$nombre}' eliminada exitosamente.");
    }
}
