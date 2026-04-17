<?php

namespace App\Http\Controllers;

use App\Models\ComplejoDeportivo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ComplejoDeportivoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtener solo los complejos del organizador autenticado
        $complejos = ComplejoDeportivo::where('organizador_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('complejos.index', compact('complejos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('complejos.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'latitud' => 'nullable|string|max:50',
            'longitud' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $validated['organizador_id'] = Auth::id();

        $complejo = ComplejoDeportivo::create($validated);

        return redirect()
            ->route('complejos.index')
            ->with('success', '¡Complejo creado exitosamente!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ComplejoDeportivo $complejo)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('view', $complejo);

        return view('complejos.show', compact('complejo'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ComplejoDeportivo $complejo)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('update', $complejo);

        return view('complejos.edit', compact('complejo'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ComplejoDeportivo $complejo)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('update', $complejo);

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'direccion' => 'required|string|max:255',
            'latitud' => 'nullable|string|max:50',
            'longitud' => 'nullable|string|max:50',
            'telefono' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
        ]);

        $complejo->update($validated);

        return redirect()
            ->route('complejos.index')
            ->with('success', '¡Complejo actualizado exitosamente!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ComplejoDeportivo $complejo)
    {
        // Verificar que el complejo pertenezca al organizador
        $this->authorize('delete', $complejo);

        $nombre = $complejo->nombre;
        $complejo->delete();

        return redirect()
            ->route('complejos.index')
            ->with('success', "Complejo '{$nombre}' eliminado exitosamente.");
    }
}
