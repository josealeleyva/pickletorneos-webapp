<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\Partido;
use App\Jobs\EnviarNotificacionPartido;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class TorneoFixtureController extends Controller
{
    /**
     * Mostrar vista de fixture del torneo
     */
    public function index(Torneo $torneo)
    {
        $this->authorize('view', $torneo);

        // Cargar categorías
        $torneo->load('categorias');

        // Cargar partidos con relaciones incluyendo categorías
        $partidos = $torneo->partidos()
            ->with(['equipo1.jugadores', 'equipo1.categoria', 'equipo2.jugadores', 'equipo2.categoria', 'grupo.categoria', 'cancha', 'juegos'])
            ->orderBy('fecha_hora')
            ->get();

        // Partidos por grupo
        $partidosPorGrupo = $partidos->groupBy('grupo.nombre');

        // Partidos por fecha
        $partidosPorFecha = $partidos
            ->filter(fn($p) => $p->fecha_hora)
            ->groupBy(fn($p) => $p->fecha_hora->format('Y-m-d'));

        $grupos = $torneo->grupos()->with(['equipos', 'categoria'])->orderBy('orden')->get();
        $canchas = $torneo->complejo->canchas()->get();

        // Calcular tabla de posiciones
        $tablaPosiciones = $this->calcularTablaPosiciones($torneo);

        return view('torneos.fixture.index', compact(
            'torneo',
            'partidos',
            'partidosPorGrupo',
            'partidosPorFecha',
            'grupos',
            'canchas',
            'tablaPosiciones'
        ));
    }

    /**
     * Calcular tabla de posiciones por grupo o categoría (soporta grupos y Liga)
     *
     * Optimizado con eager loading para evitar N+1 queries
     */
    private function calcularTablaPosiciones(Torneo $torneo)
    {
        $tablaPosiciones = [];

        // Verificar si el torneo tiene grupos o es Liga
        $tieneGrupos = $torneo->formato && $torneo->formato->tiene_grupos;

        if ($tieneGrupos) {
            // ✅ OPTIMIZACIÓN: Eager load de todos los partidos finalizados con juegos de una sola vez
            $partidosFinalizados = $torneo->partidos()
                ->with('juegos')
                ->where('estado', 'finalizado')
                ->get()
                ->groupBy('grupo_id'); // Agrupar por grupo_id para acceso rápido

            // Calcular posiciones por grupos
            $grupos = $torneo->grupos()->with(['equipos', 'categoria'])->get();

            foreach ($grupos as $grupo) {
                $posiciones = [];
                // Obtener partidos del grupo de la colección pre-cargada
                $partidosDelGrupo = $partidosFinalizados->get($grupo->id, collect());

                foreach ($grupo->equipos as $equipo) {
                    // ✅ OPTIMIZACIÓN: Filtrar partidos en memoria en lugar de hacer query
                    $partidosEquipo = $partidosDelGrupo->filter(function ($partido) use ($equipo) {
                        return $partido->equipo1_id === $equipo->id || $partido->equipo2_id === $equipo->id;
                    });

                    $stats = $this->calcularEstadisticasEquipo($equipo, $partidosEquipo);
                    $posiciones[] = $stats;
                }

                // Ordenar por: Puntos (desc) -> Diferencia sets (desc) -> Diferencia puntos (desc) -> Puntos a favor (desc)
                usort($posiciones, function ($a, $b) {
                    if ($a['puntos'] !== $b['puntos']) {
                        return $b['puntos'] - $a['puntos'];
                    }
                    if ($a['diferencia_sets'] !== $b['diferencia_sets']) {
                        return $b['diferencia_sets'] - $a['diferencia_sets'];
                    }
                    if ($a['diferencia_puntos'] !== $b['diferencia_puntos']) {
                        return $b['diferencia_puntos'] - $a['diferencia_puntos'];
                    }
                    return $b['pf'] - $a['pf'];
                });

                // Agrupar por categoría y grupo
                $categoriaId = $grupo->categoria_id;
                $categoriaNombre = $grupo->categoria ? $grupo->categoria->nombre : 'Sin categoría';
                $clave = $categoriaId . '|' . $grupo->nombre;

                // Obtener información de avance de la categoría
                $cantidadClasifican = 0;
                foreach ($torneo->categorias as $categoria) {
                    if ($categoria->id == $categoriaId) {
                        $avanceGrupoId = $categoria->pivot->avance_grupos_id;
                        if ($avanceGrupoId) {
                            $avanceGrupo = \App\Models\AvanceGrupo::find($avanceGrupoId);
                            if ($avanceGrupo) {
                                $cantidadClasifican = $avanceGrupo->cantidad_avanza_directo ?? 0;
                            }
                        }
                        break;
                    }
                }

                $tablaPosiciones[$clave] = [
                    'categoria_id' => $categoriaId,
                    'categoria_nombre' => $categoriaNombre,
                    'grupo_nombre' => $grupo->nombre,
                    'posiciones' => $posiciones,
                    'cantidad_clasifican' => $cantidadClasifican,
                    'campeon_id' => null,
                ];
            }
        } else {
            // Calcular posiciones por categoría (Liga)
            $torneo->load('categorias');

            // ✅ OPTIMIZACIÓN: Eager load de todos los partidos finalizados con juegos de una sola vez
            $partidosFinalizados = $torneo->partidos()
                ->with('juegos')
                ->where('estado', 'finalizado')
                ->get();

            foreach ($torneo->categorias as $categoria) {
                // Obtener equipos de esta categoría
                $equipos = $torneo->equipos()->where('categoria_id', $categoria->id)->get();

                if ($equipos->isEmpty()) {
                    continue;
                }

                $posiciones = [];

                foreach ($equipos as $equipo) {
                    // ✅ OPTIMIZACIÓN: Filtrar partidos en memoria en lugar de hacer query
                    $partidosEquipo = $partidosFinalizados->filter(function ($partido) use ($equipo) {
                        return $partido->equipo1_id === $equipo->id || $partido->equipo2_id === $equipo->id;
                    });

                    $stats = $this->calcularEstadisticasEquipo($equipo, $partidosEquipo);
                    $posiciones[] = $stats;
                }

                // Ordenar por: Puntos (desc) -> Diferencia sets (desc) -> Diferencia puntos (desc) -> Puntos a favor (desc)
                usort($posiciones, function ($a, $b) {
                    if ($a['puntos'] !== $b['puntos']) {
                        return $b['puntos'] - $a['puntos'];
                    }
                    if ($a['diferencia_sets'] !== $b['diferencia_sets']) {
                        return $b['diferencia_sets'] - $a['diferencia_sets'];
                    }
                    if ($a['diferencia_puntos'] !== $b['diferencia_puntos']) {
                        return $b['diferencia_puntos'] - $a['diferencia_puntos'];
                    }
                    return $b['pf'] - $a['pf'];
                });

                // Obtener campeón guardado (si existe)
                $campeonId = $categoria->pivot->campeon_id ?? null;

                $clave = $categoria->id . '|' . $categoria->nombre;

                $tablaPosiciones[$clave] = [
                    'categoria_id' => $categoria->id,
                    'categoria_nombre' => $categoria->nombre,
                    'grupo_nombre' => $categoria->nombre, // Para RR, "grupo" es la categoría
                    'posiciones' => $posiciones,
                    'cantidad_clasifican' => 0, // En RR no hay clasificación, solo campeón
                    'campeon_id' => $campeonId,
                ];
            }
        }

        return $tablaPosiciones;
    }

    /**
     * Calcular estadísticas de un equipo basado en sus partidos
     *
     * ✅ ACTUALIZADO: Incluye soporte para empates (PE)
     */
    private function calcularEstadisticasEquipo($equipo, $partidosEquipo)
    {
        $pj = $partidosEquipo->count();
        $pg = 0;
        $pe = 0; // ✅ NUEVO: Partidos empatados
        $pp = 0;
        $pf = 0; // Puntos a favor
        $pc = 0; // Puntos en contra
        $sg = 0; // Sets ganados
        $sp = 0; // Sets perdidos

        foreach ($partidosEquipo as $partido) {
            $esEquipo1 = $partido->equipo1_id === $equipo->id;

            if ($esEquipo1) {
                $pf += $partido->sets_equipo1;
                $pc += $partido->sets_equipo2;

                // ✅ ACTUALIZADO: Detectar empates
                if ($partido->equipo_ganador_id === null) {
                    $pe++; // Empate
                } elseif ($partido->equipo_ganador_id === $equipo->id) {
                    $pg++; // Ganó
                } else {
                    $pp++; // Perdió
                }
            } else {
                $pf += $partido->sets_equipo2;
                $pc += $partido->sets_equipo1;

                // ✅ ACTUALIZADO: Detectar empates
                if ($partido->equipo_ganador_id === null) {
                    $pe++; // Empate
                } elseif ($partido->equipo_ganador_id === $equipo->id) {
                    $pg++; // Ganó
                } else {
                    $pp++; // Perdió
                }
            }

            // Contar sets ganados y perdidos de los juegos
            $juegos = $partido->juegos;
            foreach ($juegos as $juego) {
                if ($esEquipo1) {
                    if ($juego->juegos_equipo1 > $juego->juegos_equipo2) {
                        $sg++;
                    } else {
                        $sp++;
                    }
                } else {
                    if ($juego->juegos_equipo2 > $juego->juegos_equipo1) {
                        $sg++;
                    } else {
                        $sp++;
                    }
                }
            }
        }

        $diferenciaSets = $sg - $sp;
        $diferenciaPuntos = $pf - $pc;
        // ✅ ACTUALIZADO: Puntos incluyen empates (Victoria: 3pts, Empate: 1pt, Derrota: 0pts)
        $puntos = ($pg * 3) + ($pe * 1);

        return [
            'equipo' => $equipo,
            'pj' => $pj,
            'pg' => $pg,
            'pe' => $pe, // ✅ NUEVO: Partidos empatados
            'pp' => $pp,
            'pf' => $pf,
            'pc' => $pc,
            'sg' => $sg,
            'sp' => $sp,
            'diferencia_sets' => $diferenciaSets,
            'diferencia_puntos' => $diferenciaPuntos,
            'diferencia' => $diferenciaPuntos, // Mantener por compatibilidad
            'puntos' => $puntos,
        ];
    }

    /**
     * Generar fixture automáticamente (detecta tipo de torneo)
     */
    public function generar(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'Solo puedes generar fixture en torneos activos.');
        }

        // Cargar formato del torneo
        $torneo->load('formato');

        // Decidir qué método de generación usar
        if ($torneo->formato && $torneo->formato->tiene_grupos) {
            // Formato con grupos (Fase de Grupos + Eliminación)
            return $this->generarFixtureConGrupos($torneo);
        } else {
            // Liga o Eliminación Directa
            return $this->generarFixtureLiga($torneo);
        }
    }

    /**
     * Generar fixture para formato con grupos (Fase de Grupos + Eliminación)
     */
    private function generarFixtureConGrupos(Torneo $torneo)
    {
        // Verificar que haya grupos configurados
        $grupos = $torneo->grupos()->with('equipos')->get();
        if ($grupos->isEmpty()) {
            return redirect()
                ->route('torneos.grupos.index', $torneo)
                ->with('error', 'Primero debes configurar los grupos del torneo.');
        }

        DB::beginTransaction();
        try {
            // Eliminar partidos previos si existen
            $torneo->partidos()->delete();

            $partidosGenerados = 0;

            // Generar partidos para cada grupo usando algoritmo de todos contra todos
            foreach ($grupos as $grupo) {
                $equipos = $grupo->equipos->values()->all();
                $cantidadEquipos = count($equipos);

                if ($cantidadEquipos < 2) {
                    continue; // No se pueden generar partidos con menos de 2 equipos
                }

                // Validar que todos los equipos del grupo pertenecen a la misma categoría
                $categoriaGrupo = $grupo->categoria_id;
                foreach ($equipos as $equipo) {
                    if ($equipo->categoria_id !== $categoriaGrupo) {
                        DB::rollBack();
                        return redirect()
                            ->route('torneos.grupos.index', $torneo)
                            ->with('error', "Error: El equipo '{$equipo->nombre}' no pertenece a la categoría del {$grupo->nombre}.");
                    }
                }

                // Algoritmo de todos contra todos
                $partidos = [];

                // Si es impar, agregar "bye" (equipo fantasma)
                if ($cantidadEquipos % 2 !== 0) {
                    $equipos[] = null; // BYE
                    $cantidadEquipos++;
                }

                $rounds = $cantidadEquipos - 1;
                $matchesPerRound = $cantidadEquipos / 2;

                for ($round = 0; $round < $rounds; $round++) {
                    for ($match = 0; $match < $matchesPerRound; $match++) {
                        $home = ($round + $match) % ($cantidadEquipos - 1);
                        $away = ($cantidadEquipos - 1 - $match + $round) % ($cantidadEquipos - 1);

                        // El último equipo rota especialmente
                        if ($match === 0) {
                            $away = $cantidadEquipos - 1;
                        }

                        // Saltar partidos con BYE
                        if ($equipos[$home] === null || $equipos[$away] === null) {
                            continue;
                        }

                        // Validación adicional: ambos equipos deben ser de la misma categoría
                        if ($equipos[$home]->categoria_id !== $equipos[$away]->categoria_id) {
                            DB::rollBack();
                            return redirect()
                                ->route('torneos.grupos.index', $torneo)
                                ->with('error', "Error: Los equipos '{$equipos[$home]->nombre}' y '{$equipos[$away]->nombre}' no pertenecen a la misma categoría.");
                        }

                        $partidos[] = [
                            'equipo1_id' => $equipos[$home]->id,
                            'equipo2_id' => $equipos[$away]->id,
                            'grupo_id' => $grupo->id,
                            'estado' => 'programado',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Insertar partidos del grupo
                if (!empty($partidos)) {
                    Partido::insert($partidos);
                    $partidosGenerados += count($partidos);
                }
            }

            DB::commit();

            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('success', "¡Fixture generado exitosamente! Se crearon {$partidosGenerados} partidos.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'Error al generar el fixture. Por favor intenta nuevamente.');
        }
    }

    /**
     * Generar fixture para formato Liga (Todos contra Todos)
     */
    private function generarFixtureLiga(Torneo $torneo)
    {
        // Verificar que haya equipos
        $equipos = $torneo->equipos()->with('categoria')->get();
        if ($equipos->isEmpty()) {
            return redirect()
                ->route('torneos.equipos.index', $torneo)
                ->with('error', 'Primero debes agregar equipos al torneo.');
        }

        // Agrupar equipos por categoría
        $equiposPorCategoria = $equipos->groupBy('categoria_id');

        DB::beginTransaction();
        try {
            // Eliminar partidos previos si existen
            $torneo->partidos()->delete();

            $partidosGenerados = 0;

            // Generar partidos para cada categoría
            foreach ($equiposPorCategoria as $categoriaId => $equiposCategoria) {
                $equiposArray = $equiposCategoria->values()->all();
                $cantidadEquipos = count($equiposArray);

                if ($cantidadEquipos < 2) {
                    continue; // No se pueden generar partidos con menos de 2 equipos
                }

                // Algoritmo de todos contra todos
                $partidos = [];

                // Si es impar, agregar "bye" (equipo fantasma)
                if ($cantidadEquipos % 2 !== 0) {
                    $equiposArray[] = null; // BYE
                    $cantidadEquipos++;
                }

                $rounds = $cantidadEquipos - 1;
                $matchesPerRound = $cantidadEquipos / 2;

                for ($round = 0; $round < $rounds; $round++) {
                    for ($match = 0; $match < $matchesPerRound; $match++) {
                        $home = ($round + $match) % ($cantidadEquipos - 1);
                        $away = ($cantidadEquipos - 1 - $match + $round) % ($cantidadEquipos - 1);

                        // El último equipo rota especialmente
                        if ($match === 0) {
                            $away = $cantidadEquipos - 1;
                        }

                        // Saltar partidos con BYE
                        if ($equiposArray[$home] === null || $equiposArray[$away] === null) {
                            continue;
                        }

                        $partidos[] = [
                            'equipo1_id' => $equiposArray[$home]->id,
                            'equipo2_id' => $equiposArray[$away]->id,
                            'grupo_id' => null, // Liga no tiene grupos
                            'estado' => 'programado',
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Insertar partidos de la categoría
                if (!empty($partidos)) {
                    Partido::insert($partidos);
                    $partidosGenerados += count($partidos);
                }
            }

            DB::commit();

            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('success', "¡Fixture generado exitosamente! Se crearon {$partidosGenerados} partidos.");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'Error al generar el fixture. Por favor intenta nuevamente.');
        }
    }

    /**
     * Determinar campeones para torneos Liga
     * Recorre cada categoría, calcula la tabla de posiciones y guarda el campeón
     */
    public function determinarCampeonesLiga(Torneo $torneo)
    {
        $torneo->load('categorias');

        foreach ($torneo->categorias as $categoria) {
            // Obtener todos los equipos de esta categoría
            $equipos = $torneo->equipos()->where('categoria_id', $categoria->id)->get();

            if ($equipos->isEmpty()) {
                continue;
            }

            $posiciones = [];

            // Calcular estadísticas para cada equipo
            foreach ($equipos as $equipo) {
                $partidosEquipo = $torneo->partidos()
                    ->with('juegos')
                    ->where(function ($query) use ($equipo) {
                        $query->where('equipo1_id', $equipo->id)
                            ->orWhere('equipo2_id', $equipo->id);
                    })
                    ->where('estado', 'finalizado')
                    ->get();

                $pj = $partidosEquipo->count();
                $pg = 0;
                $pp = 0;
                $pf = 0; // Puntos a favor
                $pc = 0; // Puntos en contra

                foreach ($partidosEquipo as $partido) {
                    $esEquipo1 = $partido->equipo1_id === $equipo->id;

                    if ($esEquipo1) {
                        $pf += $partido->sets_equipo1;
                        $pc += $partido->sets_equipo2;

                        if ($partido->equipo_ganador_id === $equipo->id) {
                            $pg++;
                        } else {
                            $pp++;
                        }
                    } else {
                        $pf += $partido->sets_equipo2;
                        $pc += $partido->sets_equipo1;

                        if ($partido->equipo_ganador_id === $equipo->id) {
                            $pg++;
                        } else {
                            $pp++;
                        }
                    }
                }

                $diferencia = $pf - $pc;
                $puntos = $pg * 3; // 3 puntos por victoria

                $posiciones[] = [
                    'equipo_id' => $equipo->id,
                    'pj' => $pj,
                    'pg' => $pg,
                    'pp' => $pp,
                    'pf' => $pf,
                    'pc' => $pc,
                    'diferencia' => $diferencia,
                    'puntos' => $puntos,
                ];
            }

            // Ordenar por: Puntos (desc) -> Diferencia (desc) -> Puntos a favor (desc)
            usort($posiciones, function ($a, $b) {
                if ($a['puntos'] !== $b['puntos']) {
                    return $b['puntos'] - $a['puntos'];
                }
                if ($a['diferencia'] !== $b['diferencia']) {
                    return $b['diferencia'] - $a['diferencia'];
                }
                return $b['pf'] - $a['pf'];
            });

            // El primero de la tabla es el campeón
            if (!empty($posiciones)) {
                $campeonId = $posiciones[0]['equipo_id'];

                // Guardar el campeón en la tabla pivot categoria_torneo
                DB::table('categoria_torneo')
                    ->where('torneo_id', $torneo->id)
                    ->where('categoria_id', $categoria->id)
                    ->update(['campeon_id' => $campeonId]);
            }
        }
    }

    /**
     * Resetear fixture (eliminar todos los partidos)
     */
    public function resetear(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'Solo puedes resetear el fixture en torneos activos.');
        }

        DB::beginTransaction();
        try {
            $torneo->partidos()->delete();
            DB::commit();

            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('success', 'Fixture reseteado. Puedes generar uno nuevo.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'Error al resetear el fixture.');
        }
    }

    /**
     * Actualizar partido individual (fecha, hora, cancha)
     */
    public function actualizarPartido(Request $request, Torneo $torneo, Partido $partido)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return response()->json(['error' => 'Solo puedes editar partidos en torneos activos.'], 403);
        }

        // Verificar que el partido pertenece al torneo
        $partidoPertenece = false;
        if ($partido->grupo) {
            // Torneo con grupos
            $partidoPertenece = $partido->grupo->torneo_id === $torneo->id;
        } else {
            // Liga - verificar por equipos
            $partidoPertenece = $torneo->partidos()->where('id', $partido->id)->exists();
        }

        if (!$partidoPertenece) {
            return response()->json(['error' => 'El partido no pertenece a este torneo.'], 400);
        }

        $validated = $request->validate([
            'fecha_hora' => 'required|date',
            'cancha_id' => 'required|exists:canchas,id',
        ]);

        // Verificar que la cancha pertenece al complejo del torneo
        $cancha = \App\Models\Cancha::find($validated['cancha_id']);
        if ($cancha->complejo_id !== $torneo->complejo_id) {
            return response()->json(['error' => 'La cancha no pertenece al complejo del torneo.'], 400);
        }

        $partido->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Partido actualizado exitosamente.',
            'partido' => $partido->load('cancha')
        ]);
    }

    /**
     * Cargar resultado de un partido
     */
    public function cargarResultado(Request $request, Torneo $torneo, Partido $partido)
    {
        $this->authorize('update', $torneo);

        // Verificar que el partido pertenece al torneo
        $partidoPertenece = false;
        if ($partido->grupo) {
            // Torneo con grupos
            $partidoPertenece = $partido->grupo->torneo_id === $torneo->id;
        } else {
            // Liga - verificar por equipos
            $partidoPertenece = $torneo->partidos()->where('id', $partido->id)->exists();
        }

        if (!$partidoPertenece) {
            return response()->json(['error' => 'El partido no pertenece a este torneo.'], 400);
        }

        // Solo permitir cargar resultados en torneos en curso o finalizados
        if (!in_array($torneo->estado, ['en_curso', 'finalizado'])) {
            return response()->json(['error' => 'Solo puedes cargar resultados en torneos en curso.'], 403);
        }

        $validated = $request->validate([
            'juegos' => 'required|array|min:1',
            'juegos.*.juegos_equipo1' => 'required|integer|min:0',
            'juegos.*.juegos_equipo2' => 'required|integer|min:0',
        ]);

        DB::beginTransaction();
        try {
            // Calcular sumatoria de puntos
            $sumatoriaPuntosEquipo1 = 0;
            $sumatoriaPuntosEquipo2 = 0;

            foreach ($validated['juegos'] as $juego) {
                $sumatoriaPuntosEquipo1 += $juego['juegos_equipo1'];
                $sumatoriaPuntosEquipo2 += $juego['juegos_equipo2'];
            }

            // Determinar ganador del partido basado en la sumatoria
            $ganadorId = null;
            if ($sumatoriaPuntosEquipo1 > $sumatoriaPuntosEquipo2) {
                $ganadorId = $partido->equipo1_id;
            } elseif ($sumatoriaPuntosEquipo2 > $sumatoriaPuntosEquipo1) {
                $ganadorId = $partido->equipo2_id;
            }
            // Si es empate, ganadorId queda null

            // Actualizar partido con la sumatoria de puntos
            $partido->update([
                'sets_equipo1' => $sumatoriaPuntosEquipo1,
                'sets_equipo2' => $sumatoriaPuntosEquipo2,
                'equipo_ganador_id' => $ganadorId,
                'estado' => 'finalizado',
            ]);

            // Guardar juegos en la tabla juegos
            foreach ($validated['juegos'] as $index => $juego) {
                \App\Models\Juego::create([
                    'partido_id' => $partido->id,
                    'numero_juego' => $index + 1,
                    'juegos_equipo1' => $juego['juegos_equipo1'],
                    'juegos_equipo2' => $juego['juegos_equipo2'],
                ]);
            }

            // Eliminar resultado tentativo si existe
            \App\Models\ResultadoTentativo::where('partido_id', $partido->id)->delete();

            DB::commit();

            // Intentar finalizar automáticamente si todos los partidos tienen resultado
            \App\Http\Controllers\TorneoController::intentarFinalizarAutomatico($torneo);

            return response()->json([
                'success' => true,
                'message' => 'Resultado cargado exitosamente.',
                'partido' => $partido->fresh(['equipo1', 'equipo2', 'equipoGanador', 'juegos'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'error' => 'Error al guardar el resultado: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Asignar fechas, horarios y canchas automáticamente
     */
    public function programar(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'activo') {
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'Solo puedes programar partidos en torneos activos.');
        }

        // Obtener partidos sin fecha
        $partidos = $torneo->partidos()->whereNull('fecha_hora')->get();

        if ($partidos->isEmpty()) {
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'No hay partidos para programar.');
        }

        // Obtener canchas del complejo
        $canchas = $torneo->complejo->canchas()->get();

        if ($canchas->isEmpty()) {
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'No hay canchas disponibles en el complejo.');
        }

        DB::beginTransaction();
        try {
            // Configuración de programación
            $fechaInicio = Carbon::parse($torneo->fecha_inicio);
            $fechaFin = Carbon::parse($torneo->fecha_fin);
            $horaInicio = 8; // 8 AM
            $horaFin = 22; // 10 PM
            $duracionPartido = 90; // 90 minutos por partido

            $fechaActual = $fechaInicio->copy()->setTime($horaInicio, 0);
            $canchaIndex = 0;

            foreach ($partidos as $partido) {
                // Verificar si se pasó del horario del día
                if ($fechaActual->hour >= $horaFin) {
                    // Pasar al día siguiente
                    $fechaActual->addDay()->setTime($horaInicio, 0);
                }

                // Verificar si se pasó de la fecha fin del torneo
                if ($fechaActual->gt($fechaFin)) {
                    break; // No se pueden programar más partidos
                }

                // Asignar fecha, hora y cancha
                $partido->update([
                    'fecha_hora' => $fechaActual->copy(),
                    'cancha_id' => $canchas[$canchaIndex]->id,
                ]);

                // Rotar cancha
                $canchaIndex = ($canchaIndex + 1) % $canchas->count();

                // Si volvimos a la primera cancha, avanzar el tiempo
                if ($canchaIndex === 0) {
                    $fechaActual->addMinutes($duracionPartido);
                }
            }

            DB::commit();

            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('success', 'Partidos programados exitosamente con fechas, horarios y canchas.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'Error al programar los partidos. Por favor intenta nuevamente.');
        }
    }

    /**
     * Enviar notificaciones de partidos a jugadores
     */
    public function enviarNotificaciones(Request $request, Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        $validated = $request->validate([
            'partido_id' => 'required|exists:partidos,id',
        ]);

        $partido = Partido::with(['equipo1.jugadores', 'equipo2.jugadores', 'grupo'])
            ->findOrFail($validated['partido_id']);

        // Verificar que el partido pertenece al torneo
        $partidoPertenece = false;
        if ($partido->grupo) {
            // Torneo con grupos
            $partidoPertenece = $partido->grupo->torneo_id === $torneo->id;
        } else {
            // Liga - verificar por equipos
            $partidoPertenece = $torneo->partidos()->where('id', $partido->id)->exists();
        }

        if (!$partidoPertenece) {
            return response()->json(['error' => 'El partido no pertenece a este torneo.'], 400);
        }

        // Verificar que el partido tiene fecha programada
        if (!$partido->fecha_hora) {
            return response()->json(['error' => 'El partido no tiene fecha programada.'], 400);
        }

        // Validar cooldown de 1 hora (excepto si el partido fue modificado después de la última notificación)
        if ($partido->ultima_notificacion) {
            $minutosDesdeUltimaNotificacion = $partido->ultima_notificacion->diffInMinutes(now());

            // Verificar si el partido fue modificado después de la última notificación
            $partidoModificadoDespuesDeNotificacion = $partido->updated_at &&
                                                       $partido->updated_at->gt($partido->ultima_notificacion);

            // Aplicar cooldown solo si no fue modificado
            if ($minutosDesdeUltimaNotificacion < 60 && !$partidoModificadoDespuesDeNotificacion) {
                $minutosRestantes = 60 - $minutosDesdeUltimaNotificacion;
                return response()->json([
                    'error' => "Debes esperar {$minutosRestantes} minutos antes de enviar otra notificación para este partido."
                ], 429);
            }
        }

        $notificacionesEnviadas = 0;

        // Enviar notificaciones a jugadores del equipo1
        if ($partido->equipo1) {
            foreach ($partido->equipo1->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $partido->equipo1_id);

                    if ($datosPartido) {
                        EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                        $notificacionesEnviadas++;
                    }
                }
            }
        }

        // Enviar notificaciones a jugadores del equipo2
        if ($partido->equipo2) {
            foreach ($partido->equipo2->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $partido->equipo2_id);

                    if ($datosPartido) {
                        EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                        $notificacionesEnviadas++;
                    }
                }
            }
        }

        // Actualizar timestamp y marca de notificación
        $partido->update([
            'notificado' => true,
            'ultima_notificacion' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => "Se enviaron {$notificacionesEnviadas} notificaciones exitosamente."
        ]);
    }

    /**
     * Marcar campeón de una categoría (para Liga)
     */
    public function marcarCampeon(Request $request, Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        if ($torneo->estado !== 'en_curso') {
            return response()->json(['error' => 'Solo puedes marcar campeones en torneos en curso.'], 403);
        }

        $validated = $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'equipo_id' => 'required|exists:equipos,id',
        ]);

        // Verificar que la categoría pertenece al torneo
        $categoriaExiste = $torneo->categorias()->where('categorias.id', $validated['categoria_id'])->exists();
        if (!$categoriaExiste) {
            return response()->json(['error' => 'La categoría no pertenece a este torneo.'], 400);
        }

        // Verificar que el equipo pertenece al torneo y a la categoría
        $equipo = $torneo->equipos()
            ->where('id', $validated['equipo_id'])
            ->where('categoria_id', $validated['categoria_id'])
            ->first();

        if (!$equipo) {
            return response()->json(['error' => 'El equipo no pertenece a esta categoría del torneo.'], 400);
        }

        //verificar que hayan terminado todos los partidos de la categoria para poder marcar el campeon
        $partidosPendientes = $torneo->partidos()
            ->where(function ($query) use ($equipo) {
                $query->whereHas('equipo1', function ($q) use ($equipo) {
                    $q->where('categoria_id', $equipo->categoria_id);
                })
                    ->orWhereHas('equipo2', function ($q) use ($equipo) {
                        $q->where('categoria_id', $equipo->categoria_id);
                    });
            })
            ->where('estado', '!=', 'finalizado')
            ->exists();
        if ($partidosPendientes) {
            return response()->json(['error' => 'No se puede marcar campeón mientras haya partidos pendientes en esta categoría.'], 400);
        }

        // Actualizar campeón en la tabla pivot
        DB::table('categoria_torneo')
            ->where('torneo_id', $torneo->id)
            ->where('categoria_id', $validated['categoria_id'])
            ->update(['campeon_id' => $validated['equipo_id']]);

        return response()->json([
            'success' => true,
            'message' => "Campeón marcado exitosamente: {$equipo->nombre}"
        ]);
    }

    /**
     * Enviar notificaciones a todos los partidos programados del fixture
     */
    public function enviarNotificacionesTodos(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        // Obtener todos los partidos programados con fecha
        $partidos = $torneo->partidos()
            ->whereNotNull('fecha_hora')
            ->where('estado', 'programado')
            ->with(['equipo1.jugadores', 'equipo2.jugadores'])
            ->get();

        if ($partidos->isEmpty()) {
            return redirect()
                ->route('torneos.fixture.index', $torneo)
                ->with('error', 'No hay partidos programados para notificar.');
        }

        $notificacionesEnviadas = 0;
        $partidosNotificados = 0;
        $partidosOmitidos = 0;

        foreach ($partidos as $partido) {
            // Validar cooldown de 1 hora (excepto si el partido fue modificado después de la última notificación)
            if ($partido->ultima_notificacion) {
                $minutosDesdeUltimaNotificacion = $partido->ultima_notificacion->diffInMinutes(now());

                // Verificar si el partido fue modificado después de la última notificación
                $partidoModificadoDespuesDeNotificacion = $partido->updated_at &&
                                                           $partido->updated_at->gt($partido->ultima_notificacion);

                // Aplicar cooldown solo si no fue modificado
                if ($minutosDesdeUltimaNotificacion < 60 && !$partidoModificadoDespuesDeNotificacion) {
                    $partidosOmitidos++;
                    continue;
                }
            }

            // Enviar notificaciones a jugadores del equipo1
            if ($partido->equipo1) {
                foreach ($partido->equipo1->jugadores as $jugador) {
                    if ($jugador->email) {
                        $datosJugador = $jugador->getDatosMail();
                        $datosPartido = $torneo->getDatosMail($partido->id, $partido->equipo1_id);

                        if ($datosPartido) {
                            EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                            $notificacionesEnviadas++;
                        }
                    }
                }
            }

            // Enviar notificaciones a jugadores del equipo2
            if ($partido->equipo2) {
                foreach ($partido->equipo2->jugadores as $jugador) {
                    if ($jugador->email) {
                        $datosJugador = $jugador->getDatosMail();
                        $datosPartido = $torneo->getDatosMail($partido->id, $partido->equipo2_id);

                        if ($datosPartido) {
                            EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                            $notificacionesEnviadas++;
                        }
                    }
                }
            }

            // Actualizar timestamp
            $partido->update([
                'notificado' => true,
                'ultima_notificacion' => now(),
            ]);

            $partidosNotificados++;
        }

        $mensaje = "Se enviaron {$notificacionesEnviadas} notificaciones para {$partidosNotificados} partidos.";
        if ($partidosOmitidos > 0) {
            $mensaje .= " Se omitieron {$partidosOmitidos} partidos por cooldown de 1 hora.";
        }

        return redirect()
            ->route('torneos.fixture.index', $torneo)
            ->with('success', $mensaje);
    }
}
