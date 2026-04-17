<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Equipo;
use App\Models\EquipoPlantilla;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TorneoEquipoController extends Controller
{
    /**
     * Mostrar vista de gestión de equipos del torneo
     * Permite ver equipos en cualquier estado, pero solo editar en borrador
     */
    public function index(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        $equipos = $torneo->equipos()->with(['jugadores', 'categoria'])->get();

        // Cargar categorías del torneo
        $torneo->load('categorias');

        // Calcular cupos
        $cuposTotales = $this->calcularCuposTotales($torneo);
        $cuposOcupados = $equipos->count();
        $cuposDisponibles = $cuposTotales - $cuposOcupados;

        // Detectar si hay grupos configurados
        $tieneGrupos = $torneo->grupos()->count() > 0;
        $tienePartidos = $torneo->partidos()->count() > 0;

        return view('torneos.equipos.index', compact(
            'torneo',
            'equipos',
            'cuposTotales',
            'cuposOcupados',
            'cuposDisponibles',
            'tieneGrupos',
            'tienePartidos'
        ));
    }

    /**
     * Mostrar formulario para agregar equipo
     */
    public function create(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes agregar equipos en torneos activos.');
        }

        // Verificar cupos disponibles
        $cuposTotales = $this->calcularCuposTotales($torneo);
        $cuposOcupados = $torneo->equipos()->count();

        if ($cuposOcupados >= $cuposTotales) {
            return redirect()
                ->route('torneos.equipos.index', $torneo)
                ->with('error', 'No hay cupos disponibles en este torneo.');
        }

        // Obtener jugadores disponibles
        // Usuarios registrados: todos los que tienen rol jugador (son globales)
        $usuarios = User::role('jugador')->get();

        // Jugadores creados manualmente: solo los del organizador autenticado
        $jugadores = Jugador::where('organizador_id', Auth::id())->get();

        // Obtener IDs de jugadores ya inscritos por categoría (para deshabilitar en el Select2)
        // Estructura: [categoria_id => [jugador_id, ...], ...]
        $jugadoresInscritosPorCategoria = DB::table('equipo_jugador')
            ->join('equipos', 'equipo_jugador.equipo_id', '=', 'equipos.id')
            ->where('equipos.torneo_id', $torneo->id)
            ->whereNull('equipos.deleted_at')
            ->select('equipos.categoria_id', 'equipo_jugador.jugador_id')
            ->get()
            ->groupBy('categoria_id')
            ->map(fn ($rows) => $rows->pluck('jugador_id')->unique()->values()->toArray())
            ->toArray();

        // Mantener lista plana para compatibilidad (jugadores inscritos en TODAS las categorías)
        $jugadoresInscritos = collect($jugadoresInscritosPorCategoria)->flatten()->unique()->toArray();

        // Calcular máximo de jugadores según deporte
        $maxJugadores = $torneo->deporte->getMaxJugadores();

        // Cargar las categorías del torneo
        $categorias = $torneo->categorias;

        return view('torneos.equipos.create', compact('torneo', 'usuarios', 'jugadores', 'maxJugadores', 'categorias', 'jugadoresInscritos', 'jugadoresInscritosPorCategoria'));
    }

    /**
     * Guardar equipo nuevo
     */
    public function store(Request $request, Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes agregar equipos en torneos activos.');
        }

        // Verificar cupos
        $cuposTotales = $this->calcularCuposTotales($torneo);
        $cuposOcupados = $torneo->equipos()->count();

        if ($cuposOcupados >= $cuposTotales) {
            return redirect()
                ->route('torneos.equipos.index', $torneo)
                ->with('error', 'No hay cupos disponibles en este torneo.');
        }

        $validated = $request->validate([
            'nombre' => 'required|string|max:255',
            'categoria_id' => 'required|exists:categorias,id',
            'jugadores' => 'required|array|min:1',
            'jugadores.*' => 'required|exists:jugadores,id',
        ], [
            'nombre.required' => 'El nombre del equipo es obligatorio.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists' => 'La categoría seleccionada no es válida.',
            'jugadores.required' => 'Debes agregar al menos un jugador al equipo.',
            'jugadores.min' => 'Debes agregar al menos un jugador al equipo.',
            'jugadores.*.exists' => 'Uno o más jugadores seleccionados no son válidos.',
        ]);

        // Verificar que la categoría pertenece al torneo
        if (! $torneo->categorias()->where('categorias.id', $validated['categoria_id'])->exists()) {
            return back()
                ->withErrors(['categoria_id' => 'La categoría seleccionada no pertenece a este torneo.'])
                ->withInput();
        }

        // Validar cupos disponibles por categoría
        $categoria = $torneo->categorias()->where('categorias.id', $validated['categoria_id'])->first();
        $equiposEnCategoria = $torneo->equipos()->where('categoria_id', $validated['categoria_id'])->count();

        // Calcular cupos de la categoría
        if ($torneo->formato && $torneo->formato->tiene_grupos) {
            // Formato con grupos
            $numeroGrupos = $categoria->pivot->numero_grupos;
            $tamanioGrupoId = $categoria->pivot->tamanio_grupo_id;
            $tamanioGrupo = \App\Models\TamanioGrupo::find($tamanioGrupoId);
            $cuposCategoria = $numeroGrupos * ($tamanioGrupo ? $tamanioGrupo->tamanio : 0);
        } else {
            // Liga o Eliminación Directa
            $cuposCategoria = $categoria->pivot->cupos_categoria ?? 0;
        }

        // Verificar si hay cupos disponibles en la categoría
        if ($equiposEnCategoria >= $cuposCategoria) {
            return back()
                ->withErrors(['categoria_id' => "No hay cupos disponibles en la categoría {$categoria->nombre}. Cupos: {$equiposEnCategoria}/{$cuposCategoria}"])
                ->withInput();
        }

        // Validar número de jugadores según deporte
        $maxJugadores = $torneo->deporte->getMaxJugadores();
        if (count($validated['jugadores']) > $maxJugadores) {
            return back()
                ->withErrors(['jugadores' => "Este deporte permite máximo {$maxJugadores} jugador(es) por equipo."])
                ->withInput();
        }

        // Validar que los jugadores no estén en otro equipo de la misma categoría (excluir soft deleted)
        $jugadoresYaInscritos = DB::table('equipo_jugador')
            ->join('equipos', 'equipo_jugador.equipo_id', '=', 'equipos.id')
            ->where('equipos.torneo_id', $torneo->id)
            ->where('equipos.categoria_id', $validated['categoria_id'])
            ->whereNull('equipos.deleted_at')
            ->whereIn('equipo_jugador.jugador_id', $validated['jugadores'])
            ->pluck('equipo_jugador.jugador_id')
            ->toArray();

        if (! empty($jugadoresYaInscritos)) {
            $jugadoresConflicto = Jugador::whereIn('id', $jugadoresYaInscritos)->get();
            $nombres = $jugadoresConflicto->pluck('nombre_completo')->join(', ');

            return back()
                ->withErrors(['jugadores' => "Los siguientes jugadores ya están en otro equipo de esta categoría: {$nombres}"])
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $equipoPlantillaId = null;

            // Solo para deportes que requieren nombre de equipo (ej: Fútbol)
            // Gestionar plantilla de equipo de forma transparente
            if ($torneo->deporte->requiereNombreEquipo()) {
                $equipoPlantilla = EquipoPlantilla::where('organizador_id', Auth::id())
                    ->where('deporte_id', $torneo->deporte_id)
                    ->where('nombre', $validated['nombre'])
                    ->first();

                if ($equipoPlantilla) {
                    // Actualizar plantilla existente con nueva formación
                    $equipoPlantilla->actualizarFormacion($validated['jugadores']);
                    $equipoPlantillaId = $equipoPlantilla->id;
                } else {
                    // Crear nueva plantilla
                    $equipoPlantilla = EquipoPlantilla::create([
                        'nombre' => $validated['nombre'],
                        'organizador_id' => Auth::id(),
                        'deporte_id' => $torneo->deporte_id,
                        'ultima_formacion' => $validated['jugadores'],
                        'veces_usado' => 1,
                        'ultimo_uso' => now(),
                    ]);
                    $equipoPlantillaId = $equipoPlantilla->id;
                }
            }

            // Crear equipo del torneo
            $equipo = $torneo->equipos()->create([
                'nombre' => $validated['nombre'],
                'categoria_id' => $validated['categoria_id'],
                'equipo_plantilla_id' => $equipoPlantillaId,
            ]);

            // Asociar jugadores con orden
            foreach ($validated['jugadores'] as $index => $jugadorId) {
                $equipo->jugadores()->attach($jugadorId, ['orden' => $index + 1]);
            }

            DB::commit();

            return redirect()
                ->route('torneos.equipos.index', $torneo)
                ->with('success', "Equipo '{$validated['nombre']}' agregado exitosamente.");
        } catch (\Exception $e) {
            DB::rollBack();

            return back()
                ->with('error', 'Error al crear el equipo. Por favor intenta nuevamente.')
                ->withInput();
        }
    }

    /**
     * Eliminar equipo del torneo
     */
    public function destroy(Torneo $torneo, Equipo $equipo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes eliminar equipos en torneos activos.');
        }

        if ($equipo->torneo_id !== $torneo->id) {
            abort(404);
        }

        // Verificar si el equipo está asignado a un grupo
        if ($equipo->grupo_id) {
            return redirect()
                ->route('torneos.equipos.index', $torneo)
                ->with('error', 'No puedes eliminar este equipo porque ya está asignado a un grupo. Primero resetea los grupos.');
        }

        $nombreEquipo = $equipo->nombre;

        // Si el equipo tiene una inscripción asociada, cancelarla (notifica a los jugadores)
        $inscripcion = \App\Models\InscripcionEquipo::where('equipo_id', $equipo->id)->first();
        if ($inscripcion) {
            app(\App\Services\InscripcionService::class)->cancelarInscripcion($inscripcion, 'organizador');
        }

        $equipo->delete();

        return redirect()
            ->route('torneos.equipos.index', $torneo)
            ->with('success', "Equipo '{$nombreEquipo}' eliminado exitosamente.");
    }

    /**
     * Calcular cupos totales del torneo (suma de todas las categorías)
     */
    private function calcularCuposTotales(Torneo $torneo)
    {
        $cuposTotales = 0;

        // Cargar categorías con configuración de pivot
        $categorias = $torneo->categorias;

        foreach ($categorias as $categoria) {
            if ($torneo->formato && $torneo->formato->tiene_grupos) {
                // Formato con grupos: calcular por grupos × tamaño
                $numeroGrupos = $categoria->pivot->numero_grupos;
                $tamanioGrupoId = $categoria->pivot->tamanio_grupo_id;

                if ($numeroGrupos && $tamanioGrupoId) {
                    $tamanioGrupo = \App\Models\TamanioGrupo::find($tamanioGrupoId);
                    if ($tamanioGrupo) {
                        $cuposTotales += $numeroGrupos * $tamanioGrupo->tamanio;
                    }
                }
            } else {
                // Liga o Eliminación Directa: usar cupos_categoria
                $cuposCategoria = $categoria->pivot->cupos_categoria ?? 0;
                $cuposTotales += $cuposCategoria;
            }
        }

        return $cuposTotales > 0 ? $cuposTotales : 0;
    }

    /**
     * Generar PDF con la planilla del equipo
     */
    public function descargarPlanilla(Torneo $torneo, Equipo $equipo)
    {
        $this->authorize('update', $torneo);

        // Verificar que el equipo pertenezca al torneo
        if ($equipo->torneo_id !== $torneo->id) {
            abort(404);
        }

        // Cargar relaciones necesarias
        $equipo->load(['jugadores' => function ($query) {
            $query->orderBy('orden');
        }]);

        // Generar PDF
        $pdf = Pdf::loadView('torneos.equipos.planilla-pdf', [
            'torneo' => $torneo,
            'equipo' => $equipo,
        ]);

        // Configurar opciones del PDF
        $pdf->setPaper('a4', 'portrait');

        // Nombre del archivo (sanitizar caracteres no válidos)
        $nombreEquipoSanitizado = str_replace(['/', '\\', ' '], '_', $equipo->nombre);
        $nombreArchivo = 'Planilla_'.$nombreEquipoSanitizado.'.pdf';

        // Descargar PDF
        return $pdf->download($nombreArchivo);
    }
}
