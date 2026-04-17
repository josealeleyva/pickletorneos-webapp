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
            ->with('deporte')
            ->orderBy('deporte_id')
            ->orderBy('nombre')
            ->get();

        // Agrupar por deporte
        $categoriasPorDeporte = $categorias->groupBy('deporte.nombre');

        return view('categorias.index', compact('categoriasPorDeporte'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $deportes = Deporte::all();

        return view('categorias.create', compact('deportes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'deporte_id' => 'required|exists:deportes,id',
            'nombre' => 'required|string|max:255',
        ]);

        // Verificar que no exista una categoría con el mismo nombre para este deporte del organizador
        $existe = Categoria::where('organizador_id', Auth::id())
            ->where('deporte_id', $validated['deporte_id'])
            ->where('nombre', $validated['nombre'])
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya tienes una categoría con ese nombre para este deporte.');
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
        $deportes = Deporte::all();

        return view('categorias.edit', compact('categoria', 'deportes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $validated = $request->validate([
            'deporte_id' => 'required|exists:deportes,id',
            'nombre' => 'required|string|max:255',
        ]);

        // Verificar que no exista otra categoría con el mismo nombre para este deporte del organizador
        $existe = Categoria::where('organizador_id', Auth::id())
            ->where('deporte_id', $validated['deporte_id'])
            ->where('nombre', $validated['nombre'])
            ->where('id', '!=', $categoria->id)
            ->exists();

        if ($existe) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Ya tienes una categoría con ese nombre para este deporte.');
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
