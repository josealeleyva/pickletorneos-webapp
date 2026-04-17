<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Grupo;
use App\Models\Equipo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TorneoGrupoController extends Controller
{
    /**
     * Mostrar vista de configuración de grupos
     */
    public function index(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Solo puedes configurar grupos en torneos activos.');
        }

        // Verificar que el torneo tenga formato con grupos
        if (!$torneo->formato || !$torneo->formato->tiene_grupos) {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Este torneo no utiliza formato de grupos.');
        }

        // Cargar categorías con configuración pivot
        $torneo->load('categorias');

        // Cargar grupos con equipos y categoría
        $grupos = $torneo->grupos()->with(['equipos.jugadores', 'categoria'])->orderBy('orden')->get();

        // Equipos sin asignar
        $equiposSinAsignar = $torneo->equipos()->whereNull('grupo_id')->with(['jugadores', 'categoria'])->get();

        // Calcular cupos totales sumando por categoría
        $cuposTotales = 0;
        foreach ($torneo->categorias as $categoria) {
            $numeroGrupos = $categoria->pivot->numero_grupos;
            $tamanioGrupoId = $categoria->pivot->tamanio_grupo_id;

            if ($numeroGrupos && $tamanioGrupoId) {
                $tamanioGrupo = \App\Models\TamanioGrupo::find($tamanioGrupoId);
                if ($tamanioGrupo) {
                    $cuposTotales += $numeroGrupos * $tamanioGrupo->tamanio;
                }
            }
        }

        $equiposTotales = $torneo->equipos()->count();
        $gruposConfigurados = $grupos->count() > 0;

        // Preparar datos de equipos por grupo para JavaScript
        $equiposPorGrupo = $grupos->mapWithKeys(function($grupo) {
            return [$grupo->nombre => $grupo->equipos->map(function($equipo) {
                return [
                    'id' => $equipo->id,
                    'nombre' => $equipo->nombre,
                    'categoria_id' => $equipo->categoria_id,
                    'jugadores' => $equipo->jugadores->pluck('nombre_completo')->join(', ')
                ];
            })];
        });

        // Detectar si hay partidos generados
        $tienePartidos = $torneo->partidos()->count() > 0;

        return view('torneos.grupos.index', compact(
            'torneo',
            'grupos',
            'equiposSinAsignar',
            'cuposTotales',
            'equiposTotales',
            'gruposConfigurados',
            'equiposPorGrupo',
            'tienePartidos'
        ));
    }

    /**
     * Generar grupos automáticamente (sorteo)
     */
    public function sortear(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('error', 'Solo puedes sortear grupos en torneos activos.');
        }

        // Cargar categorías con configuración
        $torneo->load('categorias');

        // Verificar que haya equipos
        $equipos = $torneo->equipos()->with('categoria')->get();
        if ($equipos->isEmpty()) {
            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('error', 'No hay equipos para sortear.');
        }

        // Calcular cupos totales por categoría
        $cuposTotales = 0;
        foreach ($torneo->categorias as $categoria) {
            $numeroGrupos = $categoria->pivot->numero_grupos;
            $tamanioGrupoId = $categoria->pivot->tamanio_grupo_id;

            if ($numeroGrupos && $tamanioGrupoId) {
                $tamanioGrupo = \App\Models\TamanioGrupo::find($tamanioGrupoId);
                if ($tamanioGrupo) {
                    $cuposTotales += $numeroGrupos * $tamanioGrupo->tamanio;
                }
            }
        }

        // Validar que la cantidad de equipos sea correcta
        if ($equipos->count() !== $cuposTotales) {
            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('error', "El torneo requiere exactamente {$cuposTotales} equipos. Actualmente hay {$equipos->count()}.");
        }

        DB::beginTransaction();
        try {
            // Eliminar grupos previos si existen
            $torneo->grupos()->delete();

            // Limpiar asignación de grupos de equipos
            $torneo->equipos()->update(['grupo_id' => null]);

            $nombresGrupos = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H'];
            $ordenGlobal = 1;

            // Crear grupos y sortear equipos POR CATEGORÍA
            foreach ($torneo->categorias as $categoria) {
                $numeroGrupos = $categoria->pivot->numero_grupos;
                $tamanioGrupoId = $categoria->pivot->tamanio_grupo_id;

                if (!$numeroGrupos || !$tamanioGrupoId) {
                    continue;
                }

                $tamanioGrupo = \App\Models\TamanioGrupo::find($tamanioGrupoId);
                if (!$tamanioGrupo) {
                    continue;
                }

                // Obtener equipos de esta categoría
                $equiposCategoria = $equipos->where('categoria_id', $categoria->id);

                // Validar cupos de la categoría
                $cuposCategoria = $numeroGrupos * $tamanioGrupo->tamanio;
                if ($equiposCategoria->count() !== $cuposCategoria) {
                    DB::rollBack();
                    return redirect()
                        ->route('torneos.grupos.index', $torneo)
                        ->with('error', "La categoría {$categoria->nombre} requiere {$cuposCategoria} equipos pero tiene {$equiposCategoria->count()}.");
                }

                // Crear grupos para esta categoría
                $grupos = [];
                for ($i = 0; $i < $numeroGrupos; $i++) {
                    $grupo = $torneo->grupos()->create([
                        'nombre' => 'Grupo ' . $nombresGrupos[$i] . ' - ' . $categoria->nombre,
                        'orden' => $ordenGlobal++,
                        'categoria_id' => $categoria->id,
                    ]);
                    $grupos[] = $grupo;
                }

                // Mezclar equipos de esta categoría aleatoriamente
                $equiposMezclados = $equiposCategoria->shuffle();

                // Distribuir equipos en grupos
                foreach ($grupos as $index => $grupo) {
                    $equiposDelGrupo = $equiposMezclados->slice($index * $tamanioGrupo->tamanio, $tamanioGrupo->tamanio);

                    foreach ($equiposDelGrupo as $equipo) {
                        $equipo->update(['grupo_id' => $grupo->id]);
                    }
                }
            }

            DB::commit();

            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('success', '¡Sorteo realizado exitosamente! Los equipos han sido distribuidos en los grupos por categoría.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('error', 'Error al realizar el sorteo. Por favor intenta nuevamente.');
        }
    }

    /**
     * Asignar equipo a un grupo manualmente
     */
    public function asignar(Request $request, Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return response()->json(['error' => 'Solo puedes asignar equipos en torneos activos.'], 403);
        }

        $validated = $request->validate([
            'equipo_id' => 'required|exists:equipos,id',
            'grupo_id' => 'required|exists:grupos,id',
        ]);

        $equipo = Equipo::findOrFail($validated['equipo_id']);
        $grupo = Grupo::findOrFail($validated['grupo_id']);

        // Verificar que pertenecen al torneo
        if ($equipo->torneo_id !== $torneo->id || $grupo->torneo_id !== $torneo->id) {
            return response()->json(['error' => 'El equipo o grupo no pertenecen a este torneo.'], 400);
        }

        // Verificar que el equipo y el grupo pertenecen a la misma categoría
        if ($equipo->categoria_id !== $grupo->categoria_id) {
            return response()->json(['error' => 'El equipo y el grupo deben pertenecer a la misma categoría.'], 400);
        }

        // Obtener tamaño del grupo desde la configuración de la categoría
        $categoria = $torneo->categorias()->where('categorias.id', $grupo->categoria_id)->first();
        if (!$categoria) {
            return response()->json(['error' => 'No se pudo obtener la configuración de la categoría.'], 400);
        }

        $tamanioGrupo = \App\Models\TamanioGrupo::find($categoria->pivot->tamanio_grupo_id);
        if (!$tamanioGrupo) {
            return response()->json(['error' => 'No se pudo obtener el tamaño del grupo.'], 400);
        }

        // Verificar cupo del grupo
        $equiposEnGrupo = $grupo->equipos()->count();
        if ($equiposEnGrupo >= $tamanioGrupo->tamanio) {
            return response()->json(['error' => 'El grupo ya está completo.'], 400);
        }

        $equipo->update(['grupo_id' => $grupo->id]);

        return response()->json([
            'success' => true,
            'message' => "Equipo '{$equipo->nombre}' asignado a {$grupo->nombre}."
        ]);
    }

    /**
     * Intercambiar dos equipos de grupos diferentes
     */
    public function intercambiar(Request $request, Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return response()->json(['error' => 'Solo puedes intercambiar equipos en torneos activos.'], 403);
        }

        $validated = $request->validate([
            'equipo1_id' => 'required|exists:equipos,id',
            'equipo2_id' => 'required|exists:equipos,id',
        ]);

        $equipo1 = Equipo::findOrFail($validated['equipo1_id']);
        $equipo2 = Equipo::findOrFail($validated['equipo2_id']);

        // Verificar que ambos pertenecen al torneo
        if ($equipo1->torneo_id !== $torneo->id || $equipo2->torneo_id !== $torneo->id) {
            return response()->json(['error' => 'Los equipos no pertenecen a este torneo.'], 400);
        }

        // Verificar que pertenecen a la misma categoría
        if ($equipo1->categoria_id !== $equipo2->categoria_id) {
            return response()->json(['error' => 'Solo puedes intercambiar equipos de la misma categoría.'], 400);
        }

        // Verificar que están en grupos diferentes
        if ($equipo1->grupo_id === $equipo2->grupo_id) {
            return response()->json(['error' => 'Los equipos ya están en el mismo grupo.'], 400);
        }

        DB::beginTransaction();
        try {
            // Intercambiar grupos
            $grupoTemp = $equipo1->grupo_id;
            $equipo1->update(['grupo_id' => $equipo2->grupo_id]);
            $equipo2->update(['grupo_id' => $grupoTemp]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Equipos intercambiados exitosamente."
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Error al intercambiar equipos.'], 500);
        }
    }

    /**
     * Quitar equipo de un grupo
     */
    public function quitar(Request $request, Torneo $torneo, Equipo $equipo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return response()->json(['error' => 'Solo puedes modificar equipos en torneos activos.'], 403);
        }

        if ($equipo->torneo_id !== $torneo->id) {
            return response()->json(['error' => 'El equipo no pertenece a este torneo.'], 400);
        }

        $equipo->update(['grupo_id' => null]);

        return response()->json([
            'success' => true,
            'message' => "Equipo '{$equipo->nombre}' removido del grupo."
        ]);
    }

    /**
     * Limpiar todos los grupos (resetear sorteo)
     */
    public function resetear(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('error', 'Solo puedes resetear grupos en torneos activos.');
        }

        DB::beginTransaction();
        try {
            // Eliminar grupos
            $torneo->grupos()->delete();

            // Limpiar asignación de equipos
            $torneo->equipos()->update(['grupo_id' => null]);

            DB::commit();

            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('success', 'Grupos reseteados. Puedes realizar un nuevo sorteo.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('error', 'Error al resetear los grupos.');
        }
    }
}
