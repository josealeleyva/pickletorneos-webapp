<?php

namespace App\Http\Controllers;

use App\Exports\JugadoresExport;
use App\Exports\PlantillaJugadoresExport;
use App\Imports\JugadoresImport;
use App\Models\Jugador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class JugadorController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Jugador::class, 'jugador');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Auth::user()->jugadores();

        // Búsqueda
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%{$search}%")
                    ->orWhere('apellido', 'like', "%{$search}%")
                    ->orWhere('dni', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('telefono', 'like', "%{$search}%");
            });
        }

        $jugadores = $query->with(['inscripciones', 'equipos'])
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get();

        return view('jugadores.index', compact('jugadores'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('jugadores.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'ranking' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fecha_nacimiento' => 'nullable|date_format:d/m/Y',
            'genero' => 'nullable|in:masculino,femenino,otro',
        ]);

        // Verificar que no exista un jugador con el mismo DNI para este organizador
        if (! empty($validated['dni'])) {
            $existe = Jugador::where('organizador_id', Auth::id())
                ->where('dni', $validated['dni'])
                ->exists();

            if ($existe) {
                // Si es petición AJAX/JSON, retornar error JSON
                if ($request->expectsJson()) {
                    return response()->json([
                        'message' => 'Ya tienes un jugador registrado con ese DNI.',
                        'errors' => ['dni' => ['Ya tienes un jugador registrado con ese DNI.']],
                    ], 422);
                }

                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Ya tienes un jugador registrado con ese DNI.');
            }
        }

        $validated['organizador_id'] = Auth::id();

        if (! empty($validated['fecha_nacimiento'])) {
            $validated['fecha_nacimiento'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['fecha_nacimiento'])->format('Y-m-d');
        }

        // Manejar la foto si existe
        if ($request->hasFile('foto')) {
            $validated['foto'] = $request->file('foto')->store('jugadores', 'public');
        }

        $jugador = Jugador::create($validated);

        // Si es petición AJAX/JSON, retornar JSON con el jugador creado
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Jugador creado exitosamente.',
                'id' => $jugador->id,
                'nombre' => $jugador->nombre,
                'apellido' => $jugador->apellido,
                'dni' => $jugador->dni,
                'email' => $jugador->email,
                'telefono' => $jugador->telefono,
                'ranking' => $jugador->ranking,
            ], 201);
        }

        return redirect()
            ->route('jugadores.index')
            ->with('success', 'Jugador creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Jugador $jugador)
    {
        $jugador->load('equipos', 'inscripciones.torneo');

        return view('jugadores.show', compact('jugador'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Jugador $jugador)
    {
        return view('jugadores.edit', compact('jugador'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Jugador $jugador)
    {
        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'dni' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'ranking' => 'nullable|string|max:50',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'fecha_nacimiento' => 'nullable|date_format:d/m/Y',
            'genero' => 'nullable|in:masculino,femenino,otro',
        ]);

        // Verificar que no exista otro jugador con el mismo DNI para este organizador
        if (! empty($validated['dni'])) {
            $existe = Jugador::where('organizador_id', Auth::id())
                ->where('dni', $validated['dni'])
                ->where('id', '!=', $jugador->id)
                ->exists();

            if ($existe) {
                return redirect()
                    ->back()
                    ->withInput()
                    ->with('error', 'Ya tienes otro jugador registrado con ese DNI.');
            }
        }

        // Manejar la foto si existe
        if ($request->hasFile('foto')) {
            // Eliminar foto anterior si existe
            if ($jugador->foto) {
                Storage::disk('public')->delete($jugador->foto);
            }
            $validated['foto'] = $request->file('foto')->store('jugadores', 'public');
        }

        if (! empty($validated['fecha_nacimiento'])) {
            $validated['fecha_nacimiento'] = \Carbon\Carbon::createFromFormat('d/m/Y', $validated['fecha_nacimiento'])->format('Y-m-d');
        } else {
            $validated['fecha_nacimiento'] = null;
        }

        $jugador->update($validated);

        return redirect()
            ->route('jugadores.index')
            ->with('success', 'Jugador actualizado exitosamente.');
    }

    /**
     * Exportar todos los jugadores del organizador a Excel.
     */
    public function exportar()
    {
        $this->authorize('viewAny', Jugador::class);

        $nombreArchivo = 'jugadores_'.now()->format('Y-m-d').'.xlsx';

        return Excel::download(new JugadoresExport(Auth::id()), $nombreArchivo);
    }

    /**
     * Descargar plantilla Excel para importación.
     */
    public function descargarPlantilla()
    {
        $this->authorize('create', Jugador::class);

        return Excel::download(new PlantillaJugadoresExport, 'plantilla_jugadores.xlsx');
    }

    /**
     * Procesar el archivo Excel importado.
     */
    public function procesarImportacion(Request $request)
    {
        $this->authorize('create', Jugador::class);

        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ], [
            'archivo.required' => 'Debe seleccionar un archivo para importar.',
            'archivo.mimes' => 'El archivo debe ser de tipo Excel (.xlsx, .xls) o CSV.',
            'archivo.max' => 'El archivo no debe superar los 5MB.',
        ]);

        $import = new JugadoresImport(Auth::id());

        Excel::import($import, $request->file('archivo'));

        $failures = $import->failures();
        $importedCount = $import->getImportedCount();
        $skippedDnis = $import->getSkippedDnis();

        $errores = $failures->map(function ($failure) {
            return [
                'fila' => $failure->row(),
                'errores' => $failure->errors(),
            ];
        })->toArray();

        $resumen = [
            'importados' => $importedCount,
            'errores' => $errores,
            'duplicados_dni' => $skippedDnis,
        ];

        return redirect()->route('jugadores.index')
            ->with('importacion_resumen', $resumen);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Jugador $jugador)
    {
        $nombre = $jugador->nombre_completo;

        // Eliminar foto si existe
        if ($jugador->foto) {
            Storage::disk('public')->delete($jugador->foto);
        }

        $jugador->delete();

        return redirect()
            ->route('jugadores.index')
            ->with('success', "Jugador '{$nombre}' eliminado exitosamente.");
    }
}
