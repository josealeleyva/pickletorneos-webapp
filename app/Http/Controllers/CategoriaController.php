<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Deporte;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Categoria::class, 'categoria');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categorias = Auth::user()->categorias()
            ->orderBy('nombre')
            ->get();

        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Hardcode Pickleball como único deporte
        $validated['deporte_id'] = Deporte::where('nombre', 'Pickleball')->firstOrFail()->id;

        // Verificar que no exista una categoría con el mismo nombre para el organizador
        $existe = Categoria::where('organizador_id', Auth::id())
            ->where('nombre', $validated['nombre'])
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya tienes una categoría con ese nombre.');
        }

        $validated['organizador_id'] = Auth::id();

        Categoria::create($validated);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Categoria $categoria)
    {
        $categoria->load('deporte', 'torneos');

        return view('categorias.show', compact('categoria'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
        ]);

        // Hardcode Pickleball como único deporte
        $validated['deporte_id'] = Deporte::where('nombre', 'Pickleball')->firstOrFail()->id;

        // Verificar que no exista otra categoría con el mismo nombre para el organizador
        $existe = Categoria::where('organizador_id', Auth::id())
            ->where('nombre', $validated['nombre'])
            ->where('id', '!=', $categoria->id)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya tienes una categoría con ese nombre.');
        }

        $categoria->update($validated);

        return redirect()
            ->route('categorias.index')
            ->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Categoria $categoria)
    {
        $nombre = $categoria->nombre;

        $categoria->delete();

        return redirect()
            ->route('categorias.index')
            ->with('success', "Categoría '{$nombre}' eliminada exitosamente.");
    }
}
