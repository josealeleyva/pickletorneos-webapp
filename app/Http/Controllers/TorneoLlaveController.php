<?php

namespace App\Http\Controllers;

use App\Jobs\EnviarNotificacionPartido;
use App\Models\AvanceGrupo;
use App\Models\Llave;
use App\Models\Partido;
use App\Models\Torneo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TorneoLlaveController extends Controller
{
    /**
     * Vista principal: Preview de clasificados o bracket generado
     */
    public function index(Torneo $torneo)
    {
        $this->authorize('view', $torneo);

        // Verificar que el torneo use eliminación (con o sin grupos)
        if (! $torneo->formato || $torneo->formato->esLiga()) {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('error', 'Este torneo no utiliza formato de eliminación.');
        }

        // Cargar categorías con configuración
        $torneo->load('categorias');

        // Verificar si ya hay llaves generadas
        $llavesExistentes = $torneo->llaves()->count() > 0;

        if ($llavesExistentes) {
            // Mostrar bracket generado
            return $this->showBracket($torneo);
        } else {
            // Mostrar vista previa para generar llaves
            return $this->showPreview($torneo);
        }
    }

    /**
     * Vista previa: Calcular clasificados y mostrar form editable
     */
    private function showPreview(Torneo $torneo)
    {
        // Calcular clasificados por categoría
        $clasificadosPorCategoria = [];
        $tieneGrupos = $torneo->formato && $torneo->formato->tiene_grupos;

        foreach ($torneo->categorias as $categoria) {
            if ($tieneGrupos) {
                // Fase de Grupos + Eliminación: calcular clasificados de grupos
                $clasificados = $this->calcularClasificados($torneo, $categoria);
            } else {
                // Eliminación Directa: obtener todos los equipos de la categoría
                $clasificados = $this->obtenerEquiposParaED($torneo, $categoria);
            }

            $clasificadosPorCategoria[$categoria->id] = [
                'categoria' => $categoria,
                'clasificados' => $clasificados,
                'total' => count($clasificados),
            ];
        }

        return view('torneos.llaves.preview', compact(
            'torneo',
            'clasificadosPorCategoria',
            'tieneGrupos'
        ));
    }

    /**
     * Obtener equipos para Eliminación Directa (sin grupos)
     */
    private function obtenerEquiposParaED(Torneo $torneo, $categoria)
    {
        // Obtener todos los equipos de esta categoría
        $equipos = $torneo->equipos()
            ->where('categoria_id', $categoria->id)
            ->get();

        // Aplicar seedeo aleatorio
        $equipos = $equipos->shuffle();

        // Convertir a formato similar al de clasificados para reutilizar vista
        $clasificados = [];
        $posicion = 1;

        foreach ($equipos as $equipo) {
            $clasificados[] = [
                'equipo' => $equipo,
                'grupo' => null, // No hay grupo en ED
                'posicion_grupo' => $posicion++,
                'puntos' => 0,
                'diferencia' => 0,
                'pf' => 0,
                'tipo' => 'directo', // Todos avanzan directamente
            ];
        }

        return $clasificados;
    }

    /**
     * Calcular equipos clasificados para una categoría (con grupos)
     */
    private function calcularClasificados(Torneo $torneo, $categoria)
    {
        $avanceGrupoId = $categoria->pivot->avance_grupos_id;

        if (! $avanceGrupoId) {
            return [];
        }

        $avanceGrupo = AvanceGrupo::find($avanceGrupoId);

        if (! $avanceGrupo) {
            return [];
        }

        // Obtener grupos de esta categoría
        $grupos = $torneo->grupos()
            ->where('categoria_id', $categoria->id)
            ->with('equipos')
            ->get();

        $clasificados = [];
        $equiposRestantes = collect();

        // 1. Obtener los que avanzan directo de cada grupo
        $cantidadDirecto = $avanceGrupo->cantidad_avanza_directo ?? 0;

        foreach ($grupos as $grupo) {
            $posiciones = $this->calcularPosicionesGrupo($torneo, $grupo);

            // Tomar los primeros N equipos (avanzan directo)
            for ($i = 0; $i < $cantidadDirecto && $i < count($posiciones); $i++) {
                $clasificados[] = [
                    'equipo' => $posiciones[$i]['equipo'],
                    'grupo' => $grupo,
                    'posicion_grupo' => $i + 1,
                    'puntos' => $posiciones[$i]['puntos'],
                    'diferencia' => $posiciones[$i]['diferencia'],
                    'pf' => $posiciones[$i]['pf'],
                    'tipo' => 'directo',
                ];
            }

            // Guardar los restantes para evaluarlos como "mejores"
            for ($i = $cantidadDirecto; $i < count($posiciones); $i++) {
                $equiposRestantes->push([
                    'equipo' => $posiciones[$i]['equipo'],
                    'grupo' => $grupo,
                    'posicion_grupo' => $i + 1,
                    'puntos' => $posiciones[$i]['puntos'],
                    'diferencia' => $posiciones[$i]['diferencia'],
                    'pf' => $posiciones[$i]['pf'],
                ]);
            }
        }

        // 2. Obtener los "mejores" de los restantes (ej: mejores segundos)
        $cantidadMejores = $avanceGrupo->cantidad_avanza_mejores ?? 0;

        if ($cantidadMejores > 0 && $equiposRestantes->isNotEmpty()) {
            // Ordenar por: puntos desc, diferencia desc, pf desc
            $mejores = $equiposRestantes->sortByDesc(function ($item) {
                return [$item['puntos'], $item['diferencia'], $item['pf']];
            })->take($cantidadMejores);

            foreach ($mejores as $mejor) {
                $clasificados[] = array_merge($mejor, ['tipo' => 'mejor']);
            }
        }

        return $clasificados;
    }

    /**
     * Calcular tabla de posiciones de un grupo
     *
     * Optimizado con eager loading para evitar N+1 queries
     */
    private function calcularPosicionesGrupo(Torneo $torneo, $grupo)
    {
        $posiciones = [];

        // ✅ OPTIMIZACIÓN: Cargar todos los partidos finalizados del grupo de una sola vez
        $partidosDelGrupo = $torneo->partidos()
            ->where('grupo_id', $grupo->id)
            ->where('estado', 'finalizado')
            ->get();

        foreach ($grupo->equipos as $equipo) {
            // ✅ OPTIMIZACIÓN: Filtrar partidos en memoria en lugar de hacer query
            $partidosEquipo = $partidosDelGrupo->filter(function ($partido) use ($equipo) {
                return $partido->equipo1_id === $equipo->id || $partido->equipo2_id === $equipo->id;
            });

            $pj = $partidosEquipo->count();
            $pg = 0;
            $pe = 0; // ✅ NUEVO: Partidos empatados
            $pp = 0;
            $pf = 0;
            $pc = 0;

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
            }

            $diferencia = $pf - $pc;
            // ✅ ACTUALIZADO: Puntos incluyen empates (1 punto por empate)
            $puntos = ($pg * 3) + ($pe * 1);

            $posiciones[] = [
                'equipo' => $equipo,
                'pj' => $pj,
                'pg' => $pg,
                'pe' => $pe, // ✅ NUEVO
                'pp' => $pp,
                'pf' => $pf,
                'pc' => $pc,
                'diferencia' => $diferencia,
                'puntos' => $puntos,
            ];
        }

        // Ordenar por: Puntos desc, Diferencia desc, PF desc
        usort($posiciones, function ($a, $b) {
            if ($a['puntos'] !== $b['puntos']) {
                return $b['puntos'] - $a['puntos'];
            }
            if ($a['diferencia'] !== $b['diferencia']) {
                return $b['diferencia'] - $a['diferencia'];
            }

            return $b['pf'] - $a['pf'];
        });

        return $posiciones;
    }

    /**
     * Generar llaves (crear registros en BD)
     */
    public function generate(Request $request, Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        // Validar que no existan llaves previas
        if ($torneo->llaves()->count() > 0) {
            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('error', 'Las llaves ya han sido generadas. Usa "Resetear" si deseas volver a generarlas.');
        }

        $validated = $request->validate([
            'tercer_puesto' => 'nullable|boolean',
            'categorias' => 'required|array',
            'categorias.*.categoria_id' => 'required|exists:categorias,id',
            'categorias.*.clasificados' => 'required|array',
            'categorias.*.clasificados.*' => 'required|exists:equipos,id',
        ]);

        DB::beginTransaction();
        try {
            // Calcular el punto de inicio para la programación
            // Buscar el último partido programado (de grupos o llaves previas)
            $ultimoPartido = Partido::where(function ($query) use ($torneo) {
                // Partidos de grupos del torneo
                $query->whereIn('grupo_id', function ($subQuery) use ($torneo) {
                    $subQuery->select('id')
                        ->from('grupos')
                        ->where('torneo_id', $torneo->id);
                })
                // O partidos de llaves del torneo
                    ->orWhereIn('llave_id', function ($subQuery) use ($torneo) {
                        $subQuery->select('id')
                            ->from('llaves')
                            ->where('torneo_id', $torneo->id);
                    });
            })
                ->whereNotNull('fecha_hora')
                ->orderBy('fecha_hora', 'desc')
                ->orderBy('cancha_id', 'desc')
                ->first();

            // Obtener todas las canchas del complejo
            $canchas = $torneo->complejo->canchas()->get();

            // Configuración de horarios
            $fechaInicio = Carbon::parse($torneo->fecha_inicio);
            $fechaFin = Carbon::parse($torneo->fecha_fin);
            $horaInicio = 8; // 8 AM
            $horaFin = 22; // 10 PM
            $duracionPartido = 90; // 90 minutos por partido

            // Determinar punto de inicio
            if ($ultimoPartido && $ultimoPartido->fecha_hora && ! $canchas->isEmpty()) {
                // Calcular siguiente slot disponible después del último partido
                $fechaActual = Carbon::parse($ultimoPartido->fecha_hora);

                // Encontrar el índice de la cancha del último partido
                $canchaIndex = $canchas->search(function ($cancha) use ($ultimoPartido) {
                    return $cancha->id === $ultimoPartido->cancha_id;
                });

                if ($canchaIndex === false) {
                    $canchaIndex = 0;
                } else {
                    // Rotar a la siguiente cancha
                    $canchaIndex = ($canchaIndex + 1) % $canchas->count();

                    // Si volvimos a la primera cancha, avanzar el tiempo
                    if ($canchaIndex === 0) {
                        $fechaActual->addMinutes($duracionPartido);

                        // Verificar si se pasó del horario del día
                        if ($fechaActual->hour >= $horaFin || ($fechaActual->hour === $horaFin - 1 && $fechaActual->minute > 30)) {
                            $fechaActual->addDay()->setTime($horaInicio, 0);
                        }
                    }
                }
            } else {
                // No hay partidos previos, empezar desde el inicio
                $fechaActual = $fechaInicio->copy()->setTime($horaInicio, 0);
                $canchaIndex = 0;
            }

            // Compartir estado de programación entre categorías
            $estadoProgramacion = [
                'fechaActual' => $fechaActual,
                'canchaIndex' => $canchaIndex,
                'canchas' => $canchas,
                'fechaFin' => $fechaFin,
                'horaInicio' => $horaInicio,
                'horaFin' => $horaFin,
                'duracionPartido' => $duracionPartido,
            ];

            foreach ($validated['categorias'] as $categoriaData) {
                $categoriaId = $categoriaData['categoria_id'];
                $equiposIds = $categoriaData['clasificados'];

                // Eliminar duplicados y mantener el orden
                $equiposIds = array_values(array_unique($equiposIds));

                // Obtener equipos en el orden enviado
                $equipos = [];
                foreach ($equiposIds as $equipoId) {
                    $equipos[] = \App\Models\Equipo::find($equipoId);
                }

                // Generar bracket para esta categoría con el estado de programación compartido
                $this->generarBracket($torneo, $categoriaId, $equipos, $validated['tercer_puesto'] ?? false, $estadoProgramacion);
            }

            DB::commit();

            $mjs = 'Llaves generadas exitosamente.';
            if ($torneo->formato && $torneo->formato->esEliminacionDirecta()) {
                $mjs .= ' Recuerda volver al torneo y publicarlo';
            }

            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('success', $mjs);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('error', 'Error al generar llaves: '.$e->getMessage());
        }
    }

    /**
     * Generar bracket para una categoría con manejo de BYEs
     */
    private function generarBracket($torneo, $categoriaId, $equipos, $tercerPuesto, &$estadoProgramacion)
    {
        $totalEquipos = count($equipos);

        // Calcular siguiente potencia de 2
        $potencia = 1;
        while ($potencia < $totalEquipos) {
            $potencia *= 2;
        }

        // Determinar rondas
        $rondas = [];
        $temp = $potencia;
        while ($temp > 1) {
            $rondas[] = $this->nombreRonda($temp);
            $temp /= 2;
        }

        // Usar el estado de programación compartido
        $canchas = $estadoProgramacion['canchas'];
        $fechaActual = $estadoProgramacion['fechaActual'];
        $canchaIndex = $estadoProgramacion['canchaIndex'];
        $fechaFin = $estadoProgramacion['fechaFin'];
        $horaInicio = $estadoProgramacion['horaInicio'];
        $horaFin = $estadoProgramacion['horaFin'];
        $duracionPartido = $estadoProgramacion['duracionPartido'];

        // Crear llaves de primera ronda
        $llavesPrimeraRonda = [];
        $ordenLlave = 1;

        /**
         * Algoritmo de distribución de equipos en bracket de eliminación
         *
         * El bracket debe cumplir:
         * 1. Los mejores seeds reciben BYEs cuando el número no es potencia de 2
         * 2. Los equipos restantes se enfrentan en primera ronda
         * 3. El emparejamiento debe ser lógico (consecutivos se enfrentan)
         *
         * Estrategia:
         * - Calcular cuántos equipos avanzan directo (con BYE)
         * - Calcular cuántos equipos juegan en primera ronda
         * - Distribuir estratégicamente en el bracket
         *
         * Ejemplo con 10 equipos (potencia 16):
         * - Equipos con BYE directo: 6 (E1-E6)
         * - Equipos que juegan: 4 (E7-E10, 2 partidos)
         *
         * Emparejamientos primera ronda:
         * - Llave 1: E1 vs BYE -> E1 avanza
         * - Llave 2: E2 vs BYE -> E2 avanza
         * - Llave 3: E3 vs BYE -> E3 avanza
         * - Llave 4: E4 vs BYE -> E4 avanza
         * - Llave 5: E5 vs BYE -> E5 avanza
         * - Llave 6: E6 vs BYE -> E6 avanza
         * - Llave 7: E7 vs E8 -> juegan
         * - Llave 8: E9 vs E10 -> juegan
         */
        $byes = $potencia - $totalEquipos;
        $equiposConBye = $byes; // Equipos que avanzan directo
        $equiposQueJuegan = $totalEquipos - $equiposConBye; // Equipos que juegan en primera ronda
        $partidosPrimeraRonda = $equiposQueJuegan / 2; // Cantidad de partidos reales

        // Crear array de posiciones en el bracket
        $equiposEnBracket = array_fill(0, $potencia, null);

        // Distribuir equipos:
        // - Los primeros $equiposConBye equipos reciben BYE (mejores seeds)
        // - Los restantes se distribuyen consecutivamente para enfrentarse

        $equipoIndex = 0;

        // Los primeros equipos con BYE
        for ($i = 0; $i < $equiposConBye; $i++) {
            $equiposEnBracket[$i] = $equipos[$equipoIndex++];
        }

        // Los equipos que juegan se colocan consecutivamente
        // para que se enfrenten entre sí con el emparejamiento estándar
        for ($i = $equiposConBye; $i < $totalEquipos; $i++) {
            $equiposEnBracket[$i] = $equipos[$equipoIndex++];
        }

        // Crear llaves con el emparejamiento estándar
        $numLlavesPrimeraRonda = $potencia / 2;

        for ($i = 0; $i < $numLlavesPrimeraRonda; $i++) {
            $pos1 = $i;
            $pos2 = $potencia - 1 - $i;

            $equipo1 = $equiposEnBracket[$pos1];
            $equipo2 = $equiposEnBracket[$pos2];

            // Solo crear llave si al menos uno de los equipos existe
            // (evitamos crear llaves BYE vs BYE)
            if (! $equipo1 && ! $equipo2) {
                continue;
            }

            $llave = Llave::create([
                'torneo_id' => $torneo->id,
                'categoria_id' => $categoriaId,
                'orden' => $ordenLlave++,
                'ronda' => $rondas[0],
                'equipo1_id' => $equipo1 ? $equipo1->id : null,
                'equipo2_id' => $equipo2 ? $equipo2->id : null,
            ]);

            // Crear partido automáticamente si ambos equipos están definidos
            if ($equipo1 && $equipo2 && ! $canchas->isEmpty()) {
                // Verificar si se pasó del horario del día antes de programar
                if ($fechaActual->hour >= $horaFin || ($fechaActual->hour === $horaFin - 1 && $fechaActual->minute > 30)) {
                    // Pasar al día siguiente
                    $fechaActual->addDay()->setTime($horaInicio, 0);
                    $canchaIndex = 0; // Resetear índice de cancha al cambiar de día
                }

                // Verificar si se pasó de la fecha fin del torneo
                if ($fechaActual->lte($fechaFin)) {
                    Partido::create([
                        'llave_id' => $llave->id,
                        'equipo1_id' => $equipo1->id,
                        'equipo2_id' => $equipo2->id,
                        'fecha_hora' => $fechaActual->copy(),
                        'cancha_id' => $canchas[$canchaIndex]->id,
                        'estado' => 'programado',
                    ]);

                    // Rotar cancha
                    $canchaIndex = ($canchaIndex + 1) % $canchas->count();

                    // Si volvimos a la primera cancha, avanzar el tiempo
                    if ($canchaIndex === 0) {
                        $fechaActual->addMinutes($duracionPartido);
                    }
                }
            }

            $llavesPrimeraRonda[] = $llave;
        }

        // Crear llaves de rondas siguientes
        $llavesRondaAnterior = $llavesPrimeraRonda;

        for ($r = 1; $r < count($rondas); $r++) {
            $llavesRondaActual = [];

            for ($i = 0; $i < count($llavesRondaAnterior); $i += 2) {
                $llave = Llave::create([
                    'torneo_id' => $torneo->id,
                    'categoria_id' => $categoriaId,
                    'orden' => $ordenLlave++,
                    'ronda' => $rondas[$r],
                ]);

                // Conectar llaves anteriores con esta
                $llavesRondaAnterior[$i]->update(['proxima_llave_id' => $llave->id]);
                if (isset($llavesRondaAnterior[$i + 1])) {
                    $llavesRondaAnterior[$i + 1]->update(['proxima_llave_id' => $llave->id]);
                }

                $llavesRondaActual[] = $llave;
            }

            $llavesRondaAnterior = $llavesRondaActual;
        }

        // Partido por 3er puesto (si está habilitado)
        if ($tercerPuesto && count($rondas) >= 2) {
            // Obtener semifinales (penúltima ronda)
            $semifinales = Llave::where('torneo_id', $torneo->id)
                ->where('categoria_id', $categoriaId)
                ->where('ronda', $rondas[count($rondas) - 2])
                ->get();

            if ($semifinales->count() === 2) {
                Llave::create([
                    'torneo_id' => $torneo->id,
                    'categoria_id' => $categoriaId,
                    'orden' => $ordenLlave++,
                    'ronda' => '3er Puesto',
                ]);
            }
        }

        // Procesar avances automáticos por BYE y programar partidos de segunda ronda
        $this->procesarAvancesBye($torneo, $categoriaId, $estadoProgramacion);

        // Enviar notificaciones automáticas para partidos programados de primera ronda
        $this->enviarNotificacionesAutomaticas($torneo, $categoriaId);

        // El estado ya se actualiza dentro de procesarAvancesBye y programarPartidosAutomaticamente
        // Solo necesitamos actualizar las variables locales si no se modificaron dentro
        $estadoProgramacion['fechaActual'] = $fechaActual;
        $estadoProgramacion['canchaIndex'] = $canchaIndex;
    }

    /**
     * Procesar avance automático cuando una llave tiene BYE después de cargar resultado
     * Se ejecuta recursivamente para avanzar en cascada
     */
    private function procesarAvanceAutomaticoPorBye($llave, $torneo, $partidoAnterior)
    {
        // Recargar la llave para tener datos actualizados
        $llave = $llave->fresh();

        // Si la llave tiene un equipo y el otro es null
        if (($llave->equipo1_id && ! $llave->equipo2_id) || (! $llave->equipo1_id && $llave->equipo2_id)) {
            // Verificar si es un BYE real o está esperando resultado
            $esByeReal = $this->verificarSiEsByeReal($llave);

            if ($esByeReal) {
                $ganadorId = $llave->equipo1_id ?? $llave->equipo2_id;

                // Si tiene una próxima llave, avanzar al ganador
                if ($llave->proxima_llave_id) {
                    $proximaLlave = Llave::find($llave->proxima_llave_id);

                    if ($proximaLlave) {
                        // Determinar si va a equipo1 o equipo2 de la próxima llave
                        $llavesAnteriores = Llave::where('proxima_llave_id', $proximaLlave->id)
                            ->orderBy('orden')
                            ->get();

                        if ($llavesAnteriores->count() >= 2) {
                            if ($llavesAnteriores[0]->id == $llave->id) {
                                $proximaLlave->update(['equipo1_id' => $ganadorId]);
                            } else {
                                $proximaLlave->update(['equipo2_id' => $ganadorId]);
                            }

                            // Llamada recursiva para verificar si la próxima llave también tiene BYE
                            $this->procesarAvanceAutomaticoPorBye($proximaLlave, $torneo, $partidoAnterior);
                        }
                    }
                }
            }
        }
        // Si ambos equipos están definidos, crear el partido
        elseif ($llave->equipo1_id && $llave->equipo2_id && ! $llave->partido) {
            // Calcular fecha/hora para la próxima ronda (después del partido anterior)
            $fechaProximoPartido = $partidoAnterior->fecha_hora->copy()->addDays(2);

            $canchas = $torneo->complejo->canchas()->get();
            if (! $canchas->isEmpty()) {
                Partido::create([
                    'llave_id' => $llave->id,
                    'equipo1_id' => $llave->equipo1_id,
                    'equipo2_id' => $llave->equipo2_id,
                    'fecha_hora' => $fechaProximoPartido,
                    'cancha_id' => $canchas->random()->id,
                    'estado' => 'programado',
                ]);
            }
        }
    }

    /**
     * Verificar si una llave tiene un BYE real o está esperando resultado
     * Retorna true solo si es un BYE real
     */
    private function verificarSiEsByeReal($llave)
    {
        // Obtener las llaves que alimentan a esta llave
        $llavesAnteriores = Llave::where('proxima_llave_id', $llave->id)->get();

        if ($llavesAnteriores->isEmpty()) {
            // Es una llave de primera ronda, el null es un BYE real
            return true;
        }

        // Verificar cuál posición está null (equipo1 o equipo2)
        $posicionNull = $llave->equipo1_id ? 'equipo2' : 'equipo1';

        // Determinar qué llave anterior debería llenar esa posición
        $llavesAnterioresOrdenadas = $llavesAnteriores->sortBy('orden');
        $llaveQueDeberiaLlenar = $posicionNull === 'equipo1'
            ? $llavesAnterioresOrdenadas->first()
            : $llavesAnterioresOrdenadas->last();

        if (! $llaveQueDeberiaLlenar) {
            return true; // BYE real por defecto si no hay llave anterior
        }

        // Si la llave anterior tiene ambos equipos null (BYE vs BYE), es BYE real
        if (! $llaveQueDeberiaLlenar->equipo1_id && ! $llaveQueDeberiaLlenar->equipo2_id) {
            return true;
        }

        // Si la llave anterior tiene solo un equipo (el otro es BYE), es BYE real
        if (($llaveQueDeberiaLlenar->equipo1_id && ! $llaveQueDeberiaLlenar->equipo2_id) ||
            (! $llaveQueDeberiaLlenar->equipo1_id && $llaveQueDeberiaLlenar->equipo2_id)) {
            return true;
        }

        // Si la llave anterior tiene ambos equipos, está esperando resultado
        return false;
    }

    /**
     * Procesar avances automáticos cuando un equipo tiene BYE
     * Solo procesa la primera ronda para evitar avanzar equipos en llaves que están esperando resultados
     */
    private function procesarAvancesBye($torneo, $categoriaId, &$estadoProgramacion)
    {
        // Obtener la primera ronda (donde están los BYEs reales)
        $primeraRonda = Llave::where('torneo_id', $torneo->id)
            ->where('categoria_id', $categoriaId)
            ->orderBy('orden')
            ->first();

        if (! $primeraRonda) {
            return;
        }

        // Obtener solo las llaves de la primera ronda
        $llavesPrimeraRonda = Llave::where('torneo_id', $torneo->id)
            ->where('categoria_id', $categoriaId)
            ->where('ronda', $primeraRonda->ronda)
            ->orderBy('orden')
            ->get();

        // Colección de llaves que necesitan partido programado
        $llavesParaProgramar = collect();

        // Procesar cada llave de la primera ronda
        foreach ($llavesPrimeraRonda as $llave) {
            // Si la llave tiene un equipo y el otro es BYE (null)
            if (($llave->equipo1_id && ! $llave->equipo2_id) || (! $llave->equipo1_id && $llave->equipo2_id)) {
                // Determinar el ganador (el equipo que no es BYE)
                $ganadorId = $llave->equipo1_id ?? $llave->equipo2_id;

                // Si tiene una próxima llave, avanzar al ganador
                if ($llave->proxima_llave_id) {
                    $proximaLlave = Llave::find($llave->proxima_llave_id);

                    if ($proximaLlave) {
                        // Determinar si va a equipo1 o equipo2 de la próxima llave
                        $llavesAnteriores = Llave::where('proxima_llave_id', $proximaLlave->id)
                            ->orderBy('orden')
                            ->get();

                        if ($llavesAnteriores->count() >= 2) {
                            if ($llavesAnteriores[0]->id == $llave->id) {
                                // Esta es la primera llave, el ganador va a equipo1
                                $proximaLlave->update(['equipo1_id' => $ganadorId]);
                            } else {
                                // Esta es la segunda llave, el ganador va a equipo2
                                $proximaLlave->update(['equipo2_id' => $ganadorId]);
                            }

                            // Recargar la llave para tener los datos actualizados
                            $proximaLlave = $proximaLlave->fresh();

                            // Si la próxima llave ahora tiene ambos equipos, programar partido
                            if ($proximaLlave->equipo1_id && $proximaLlave->equipo2_id && ! $proximaLlave->partido) {
                                $llavesParaProgramar->push($proximaLlave);
                            }
                        }
                    }
                }
            }
        }

        // Programar partidos para las llaves que tienen ambos equipos definidos
        if ($llavesParaProgramar->isNotEmpty()) {
            $this->programarPartidosAutomaticamente($llavesParaProgramar, $estadoProgramacion);
        }
    }

    /**
     * Programar partidos automáticamente para llaves que tienen ambos equipos
     */
    private function programarPartidosAutomaticamente($llaves, &$estadoProgramacion)
    {
        $canchas = $estadoProgramacion['canchas'];
        $fechaActual = $estadoProgramacion['fechaActual'];
        $canchaIndex = $estadoProgramacion['canchaIndex'];
        $fechaFin = $estadoProgramacion['fechaFin'];
        $horaInicio = $estadoProgramacion['horaInicio'];
        $horaFin = $estadoProgramacion['horaFin'];
        $duracionPartido = $estadoProgramacion['duracionPartido'];

        if ($canchas->isEmpty()) {
            return;
        }

        foreach ($llaves as $llave) {
            // Verificar si se pasó del horario del día
            if ($fechaActual->hour >= $horaFin || ($fechaActual->hour === $horaFin - 1 && $fechaActual->minute > 30)) {
                $fechaActual->addDay()->setTime($horaInicio, 0);
                $canchaIndex = 0;
            }

            // Verificar si está dentro del rango de fechas del torneo
            if ($fechaActual->lte($fechaFin)) {
                Partido::create([
                    'llave_id' => $llave->id,
                    'equipo1_id' => $llave->equipo1_id,
                    'equipo2_id' => $llave->equipo2_id,
                    'fecha_hora' => $fechaActual->copy(),
                    'cancha_id' => $canchas[$canchaIndex]->id,
                    'estado' => 'programado',
                ]);

                // Rotar cancha
                $canchaIndex = ($canchaIndex + 1) % $canchas->count();

                // Si volvimos a la primera cancha, avanzar el tiempo
                if ($canchaIndex === 0) {
                    $fechaActual->addMinutes($duracionPartido);
                }
            }
        }

        // Actualizar el estado de programación
        $estadoProgramacion['fechaActual'] = $fechaActual;
        $estadoProgramacion['canchaIndex'] = $canchaIndex;
    }

    /**
     * Obtener nombre de ronda según cantidad de equipos
     */
    private function nombreRonda($equipos)
    {
        return match ($equipos) {
            2 => 'Final',
            4 => 'Semifinal',
            8 => 'Cuartos de Final',
            16 => 'Octavos de Final',
            32 => 'Dieciseisavos de Final',
            default => "Ronda de $equipos",
        };
    }

    /**
     * Mostrar bracket generado
     */
    private function showBracket(Torneo $torneo)
    {
        // Cargar llaves con relaciones
        $llavesPorCategoria = [];

        foreach ($torneo->categorias as $categoria) {
            $llaves = $torneo->llaves()
                ->where('categoria_id', $categoria->id)
                ->with(['equipo1', 'equipo2', 'partido.juegos', 'partido.cancha.complejo', 'proximaLlave', 'llavesAnteriores'])
                ->orderBy('orden')
                ->get();

            // Agrupar por ronda
            $llavesPorRonda = $llaves->groupBy('ronda');

            $llavesPorCategoria[$categoria->id] = [
                'categoria' => $categoria,
                'llaves_por_ronda' => $llavesPorRonda,
                'rondas' => $llavesPorRonda->keys()->toArray(),
            ];
        }

        $canchas = $torneo->complejo->canchas;

        return view('torneos.llaves.bracket', compact(
            'torneo',
            'llavesPorCategoria',
            'canchas'
        ));
    }

    /**
     * Resetear llaves (eliminar todas)
     */
    public function reset(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        // Verificar si hay partidos finalizados
        $partidosFinalizados = Partido::whereIn('llave_id', $torneo->llaves->pluck('id'))
            ->where('estado', 'finalizado')
            ->count();

        if ($partidosFinalizados > 0) {
            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('error', 'No se pueden resetear las llaves porque ya hay partidos finalizados.');
        }

        // Eliminar llaves y partidos asociados
        DB::beginTransaction();
        try {
            Partido::whereIn('llave_id', $torneo->llaves->pluck('id'))->delete();
            $torneo->llaves()->delete();

            DB::commit();

            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('success', 'Llaves reseteadas exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('error', 'Error al resetear llaves.');
        }
    }

    /**
     * Programar partido de llave
     */
    public function programarPartido(Request $request, Torneo $torneo, Llave $llave)
    {
        $this->authorize('update', $torneo);

        $validated = $request->validate([
            'fecha_hora' => 'required|date',
            'cancha_id' => 'nullable|exists:canchas,id',
        ]);

        DB::beginTransaction();
        try {
            // Verificar que la llave tenga ambos equipos
            if (! $llave->equipo1_id || ! $llave->equipo2_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede programar el partido hasta que ambos equipos estén definidos.',
                ], 422);
            }

            // Crear o actualizar partido
            $partido = $llave->partido;

            if ($partido) {
                // Actualizar partido existente
                $partido->update([
                    'fecha_hora' => $validated['fecha_hora'],
                    'cancha_id' => $validated['cancha_id'] ?? null,
                    'estado' => 'programado',
                ]);
            } else {
                // Crear nuevo partido
                $partido = Partido::create([
                    'llave_id' => $llave->id,
                    'equipo1_id' => $llave->equipo1_id,
                    'equipo2_id' => $llave->equipo2_id,
                    'fecha_hora' => $validated['fecha_hora'],
                    'cancha_id' => $validated['cancha_id'] ?? null,
                    'estado' => 'programado',
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Partido programado exitosamente.',
                'partido' => $partido->load('cancha'),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al programar partido: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cargar resultado de partido de llave y avanzar ganador
     */
    public function cargarResultado(Request $request, Torneo $torneo, Llave $llave)
    {
        $this->authorize('update', $torneo);

        // ✅ Validación diferenciada por deporte
        $rules = [
            'juegos' => 'required|array|min:1|max:3', // Máximo 3 juegos
            'juegos.*.juegos_equipo1' => 'required|integer|min:0',
            'juegos.*.juegos_equipo2' => 'required|integer|min:0',
        ];

        // ✅ NUEVO: Solo para fútbol
        if ($torneo->deporte->esFutbol()) {
            $rules['juegos.*.tipo_juego'] = 'required|in:partido,ida,vuelta,penales';
        }

        $validated = $request->validate($rules);

        DB::beginTransaction();
        try {
            $partido = $llave->partido;

            if (! $partido) {
                return response()->json([
                    'success' => false,
                    'message' => 'No existe un partido programado para esta llave.',
                ], 422);
            }

            // ✅ NUEVO: Validaciones específicas para fútbol
            if ($torneo->deporte->esFutbol()) {
                $erroresValidacion = $this->validarJuegosFutbol($validated['juegos']);
                if ($erroresValidacion) {
                    DB::rollBack();

                    return response()->json([
                        'success' => false,
                        'message' => $erroresValidacion,
                    ], 422);
                }
            }

            // Calcular totales y determinar ganador
            $resultado = $this->calcularResultadoPartido($validated['juegos'], $partido, $torneo);

            // Actualizar partido
            $partido->update([
                'sets_equipo1' => $resultado['total_equipo1'],
                'sets_equipo2' => $resultado['total_equipo2'],
                'equipo_ganador_id' => $resultado['ganador_id'],
                'estado' => 'finalizado',
                'observaciones' => $resultado['observaciones'], // ✅ Generado automáticamente
            ]);

            // Guardar juegos con tipo
            foreach ($validated['juegos'] as $index => $juego) {
                \App\Models\Juego::create([
                    'partido_id' => $partido->id,
                    'numero_juego' => $index + 1,
                    'juegos_equipo1' => $juego['juegos_equipo1'],
                    'juegos_equipo2' => $juego['juegos_equipo2'],
                    'tipo_juego' => $juego['tipo_juego'] ?? ($torneo->deporte->usaSets() ? 'set' : 'partido'),
                ]);
            }

            // Eliminar resultado tentativo si existe
            \App\Models\ResultadoTentativo::where('partido_id', $partido->id)->delete();

            // Determinar el perdedor
            $ganadorId = $resultado['ganador_id']; // ✅ ACTUALIZADO
            $perdedorId = null;
            if ($ganadorId) {
                $perdedorId = ($ganadorId === $partido->equipo1_id) ? $partido->equipo2_id : $partido->equipo1_id;
            }

            // Avanzar ganador a la siguiente llave
            if ($ganadorId && $llave->proxima_llave_id) {
                $proximaLlave = $llave->proximaLlave;

                // Determinar si el ganador va a equipo1 o equipo2 de la próxima llave
                // La llave actual es la primera o segunda de las dos que alimentan la próxima
                $llavesAnteriores = $proximaLlave->llavesAnteriores()->orderBy('orden')->get();

                if ($llavesAnteriores->count() >= 2) {
                    if ($llavesAnteriores[0]->id == $llave->id) {
                        // Esta es la primera llave, el ganador va a equipo1
                        $proximaLlave->update(['equipo1_id' => $ganadorId]);
                    } else {
                        // Esta es la segunda llave, el ganador va a equipo2
                        $proximaLlave->update(['equipo2_id' => $ganadorId]);
                    }

                    // Verificar si la próxima llave tiene un BYE y avanzar automáticamente
                    $this->procesarAvanceAutomaticoPorBye($proximaLlave, $torneo, $partido);
                }
            }

            // Manejar partido por 3er puesto: si es semifinal, colocar perdedor en llave de 3er puesto
            if ($perdedorId && $llave->ronda === 'Semifinal') {
                // Buscar si existe una llave de 3er puesto en esta categoría
                $llaveTercerPuesto = Llave::where('torneo_id', $torneo->id)
                    ->where('categoria_id', $llave->categoria_id)
                    ->where('ronda', '3er Puesto')
                    ->first();

                if ($llaveTercerPuesto) {
                    // Determinar si es la primera o segunda semifinal finalizada
                    $semifinales = Llave::where('torneo_id', $torneo->id)
                        ->where('categoria_id', $llave->categoria_id)
                        ->where('ronda', 'Semifinal')
                        ->orderBy('orden')
                        ->get();

                    if ($semifinales->count() === 2) {
                        if ($semifinales[0]->id == $llave->id) {
                            // Primera semifinal, el perdedor va a equipo1
                            $llaveTercerPuesto->update(['equipo1_id' => $perdedorId]);
                        } else {
                            // Segunda semifinal, el perdedor va a equipo2
                            $llaveTercerPuesto->update(['equipo2_id' => $perdedorId]);
                        }

                        // Recargar la llave para verificar si ya tiene ambos equipos
                        $llaveTercerPuesto = $llaveTercerPuesto->fresh();

                        // Si ahora tiene ambos equipos, crear el partido automáticamente
                        if ($llaveTercerPuesto->equipo1_id && $llaveTercerPuesto->equipo2_id && ! $llaveTercerPuesto->partido) {
                            // Calcular fecha/hora para el partido de 3er puesto (un día antes de la final)
                            $proximaLlave = $llave->proximaLlave;
                            $fechaTercerPuesto = $partido->fecha_hora->copy()->addDays(1);

                            $canchas = $torneo->complejo->canchas()->get();
                            if (! $canchas->isEmpty()) {
                                Partido::create([
                                    'llave_id' => $llaveTercerPuesto->id,
                                    'equipo1_id' => $llaveTercerPuesto->equipo1_id,
                                    'equipo2_id' => $llaveTercerPuesto->equipo2_id,
                                    'fecha_hora' => $fechaTercerPuesto,
                                    'cancha_id' => $canchas->first()->id,
                                    'estado' => 'programado',
                                ]);
                            }
                        }
                    }
                }
            }

            DB::commit();

            if ($torneo->dupr_requerido) {
                \App\Jobs\SincronizarResultadoDuprJob::dispatch($partido->id);
            }

            // Intentar finalizar automáticamente si todos los partidos tienen resultado
            \App\Http\Controllers\TorneoController::intentarFinalizarAutomatico($torneo);

            return response()->json([
                'success' => true,
                'message' => $resultado['mensaje'],
                'partido' => $partido->fresh(['equipo1', 'equipo2', 'equipoGanador', 'juegos']),
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Error al cargar resultado: '.$e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enviar notificaciones de partidos de llave a jugadores
     */
    public function enviarNotificaciones(Request $request, Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        $validated = $request->validate([
            'llave_id' => 'required|exists:llaves,id',
        ]);

        $llave = Llave::with(['equipo1.jugadores', 'equipo2.jugadores', 'partido'])
            ->findOrFail($validated['llave_id']);

        // Verificar que la llave pertenece al torneo
        if ($llave->torneo_id !== $torneo->id) {
            return response()->json(['error' => 'La llave no pertenece a este torneo.'], 400);
        }

        // Verificar que la llave tiene partido con fecha programada
        if (! $llave->partido || ! $llave->partido->fecha_hora) {
            return response()->json(['error' => 'La llave no tiene partido programado.'], 400);
        }

        $partido = $llave->partido;

        // Validar cooldown de 1 hora (excepto si el partido fue modificado después de la última notificación)
        if ($partido->ultima_notificacion) {
            $minutosDesdeUltimaNotificacion = $partido->ultima_notificacion->diffInMinutes(now());

            // Verificar si el partido fue modificado después de la última notificación
            $partidoModificadoDespuesDeNotificacion = $partido->updated_at &&
                                                       $partido->updated_at->gt($partido->ultima_notificacion);

            // Aplicar cooldown solo si no fue modificado
            if ($minutosDesdeUltimaNotificacion < 60 && ! $partidoModificadoDespuesDeNotificacion) {
                $minutosRestantes = 60 - $minutosDesdeUltimaNotificacion;

                return response()->json([
                    'error' => "Debes esperar {$minutosRestantes} minutos antes de enviar otra notificación para este partido.",
                ], 429);
            }
        }

        $notificacionesEnviadas = 0;

        // Enviar notificaciones a jugadores del equipo1
        if ($llave->equipo1) {
            foreach ($llave->equipo1->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $llave->equipo1_id);

                    if ($datosPartido) {
                        EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                        $notificacionesEnviadas++;
                    }
                }
            }
        }

        // Enviar notificaciones a jugadores del equipo2
        if ($llave->equipo2) {
            foreach ($llave->equipo2->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $llave->equipo2_id);

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
            'message' => "Se enviaron {$notificacionesEnviadas} notificaciones exitosamente.",
        ]);
    }

    /**
     * Enviar notificaciones a todos los partidos programados de las llaves
     */
    public function enviarNotificacionesTodos(Torneo $torneo)
    {
        $this->authorize('update', $torneo);

        // Obtener todas las llaves con partidos programados
        $llaves = $torneo->llaves()
            ->whereHas('partido', function ($q) {
                $q->where('estado', 'programado')->whereNotNull('fecha_hora');
            })
            ->with(['equipo1.jugadores', 'equipo2.jugadores', 'partido'])
            ->get();

        if ($llaves->isEmpty()) {
            return redirect()
                ->route('torneos.llaves.index', $torneo)
                ->with('error', 'No hay partidos programados para notificar en las llaves.');
        }

        $notificacionesEnviadas = 0;
        $llaveNotificadas = 0;
        $llavesOmitidas = 0;

        foreach ($llaves as $llave) {
            if (! $llave->equipo1 || ! $llave->equipo2 || ! $llave->partido) {
                continue;
            }

            $partido = $llave->partido;

            // Validar cooldown de 1 hora (excepto si el partido fue modificado después de la última notificación)
            if ($partido->ultima_notificacion) {
                $minutosDesdeUltimaNotificacion = $partido->ultima_notificacion->diffInMinutes(now());

                // Verificar si el partido fue modificado después de la última notificación
                $partidoModificadoDespuesDeNotificacion = $partido->updated_at &&
                                                           $partido->updated_at->gt($partido->ultima_notificacion);

                // Aplicar cooldown solo si no fue modificado
                if ($minutosDesdeUltimaNotificacion < 60 && ! $partidoModificadoDespuesDeNotificacion) {
                    $llavesOmitidas++;

                    continue;
                }
            }

            // Enviar notificaciones a jugadores del equipo1
            foreach ($llave->equipo1->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $llave->equipo1_id);

                    if ($datosPartido) {
                        EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                        $notificacionesEnviadas++;
                    }
                }
            }

            // Enviar notificaciones a jugadores del equipo2
            foreach ($llave->equipo2->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $llave->equipo2_id);

                    if ($datosPartido) {
                        EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                        $notificacionesEnviadas++;
                    }
                }
            }

            // Actualizar timestamp
            $partido->update([
                'notificado' => true,
                'ultima_notificacion' => now(),
            ]);

            $llaveNotificadas++;
        }

        $mensaje = "Se enviaron {$notificacionesEnviadas} notificaciones para {$llaveNotificadas} partidos.";
        if ($llavesOmitidas > 0) {
            $mensaje .= " Se omitieron {$llavesOmitidas} partidos por cooldown de 1 hora.";
        }

        // Si es una petición AJAX, devolver JSON
        if (request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $mensaje,
                'notificaciones_enviadas' => $notificacionesEnviadas,
                'llaves_notificadas' => $llaveNotificadas,
                'llaves_omitidas' => $llavesOmitidas,
            ]);
        }

        return redirect()
            ->route('torneos.llaves.index', $torneo)
            ->with('success', $mensaje);
    }

    /**
     * Enviar notificaciones automáticas para partidos programados de una categoría
     */
    private function enviarNotificacionesAutomaticas($torneo, $categoriaId)
    {
        // Obtener todas las llaves programadas de la categoría con partidos
        $llaves = Llave::where('torneo_id', $torneo->id)
            ->where('categoria_id', $categoriaId)
            ->whereHas('partido', function ($query) {
                $query->where('estado', 'programado')
                    ->whereNotNull('fecha_hora');
            })
            ->with(['equipo1.jugadores', 'equipo2.jugadores', 'partido'])
            ->get();

        foreach ($llaves as $llave) {
            if (! $llave->partido || ! $llave->equipo1 || ! $llave->equipo2) {
                continue;
            }

            $partido = $llave->partido;

            // Enviar notificaciones a jugadores del equipo1
            foreach ($llave->equipo1->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $llave->equipo1_id);

                    if ($datosPartido) {
                        EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                    }
                }
            }

            // Enviar notificaciones a jugadores del equipo2
            foreach ($llave->equipo2->jugadores as $jugador) {
                if ($jugador->email) {
                    $datosJugador = $jugador->getDatosMail();
                    $datosPartido = $torneo->getDatosMail($partido->id, $llave->equipo2_id);

                    if ($datosPartido) {
                        EnviarNotificacionPartido::dispatch($datosJugador, $datosPartido);
                    }
                }
            }

            // Actualizar timestamp
            $partido->update([
                'notificado' => true,
                'ultima_notificacion' => now(),
            ]);
        }
    }

    /**
     * Validar juegos de fútbol (ida, vuelta, penales)
     */
    private function validarJuegosFutbol(array $juegos): ?string
    {
        $cantidadJuegos = count($juegos);

        // No más de 3 juegos
        if ($cantidadJuegos > 3) {
            return 'No se pueden agregar más de 3 juegos (Ida, Vuelta, Penales)';
        }

        // Validar tipos según cantidad
        $tipos = array_column($juegos, 'tipo_juego');

        // Si hay penales, debe ser el último
        $indexPenales = array_search('penales', $tipos);
        if ($indexPenales !== false && $indexPenales !== $cantidadJuegos - 1) {
            return 'Los penales deben ser el último juego';
        }

        // Penales no pueden empatar
        if ($indexPenales !== false) {
            $penales = $juegos[$indexPenales];
            if ($penales['juegos_equipo1'] === $penales['juegos_equipo2']) {
                return 'Los penales no pueden terminar empatados. Debe haber un ganador.';
            }
        }

        // Validar secuencia lógica
        if ($cantidadJuegos === 2) {
            // partido+vuelta, partido+penales, ida+vuelta, ida+penales
            $secuenciasValidas = [
                ['partido', 'vuelta'],
                ['partido', 'penales'],
                ['ida', 'vuelta'],
                ['ida', 'penales'],
            ];
            if (! in_array($tipos, $secuenciasValidas)) {
                return 'Secuencia de juegos inválida';
            }
        }

        if ($cantidadJuegos === 3) {
            // Solo ida+vuelta+penales
            if ($tipos !== ['ida', 'vuelta', 'penales']) {
                return 'Con 3 juegos solo se permite: Ida, Vuelta y Penales';
            }
        }

        return null; // Sin errores
    }

    /**
     * Calcular resultado del partido según los juegos
     */
    private function calcularResultadoPartido(array $juegos, $partido, $torneo): array
    {
        $totalEquipo1 = 0;
        $totalEquipo2 = 0;
        $ganadorId = null;
        $observaciones = '';
        $mensaje = 'Resultado cargado exitosamente.';

        // Sumar todos los goles/sets
        foreach ($juegos as $juego) {
            $totalEquipo1 += $juego['juegos_equipo1'];
            $totalEquipo2 += $juego['juegos_equipo2'];
        }

        $cantidadJuegos = count($juegos);
        $tipos = array_column($juegos, 'tipo_juego');

        // ✅ Para fútbol: lógica específica
        if ($torneo->deporte->esFutbol()) {
            // Caso 1: Un solo juego (partido único)
            if ($cantidadJuegos === 1) {
                if ($totalEquipo1 > $totalEquipo2) {
                    $ganadorId = $partido->equipo1_id;
                } elseif ($totalEquipo2 > $totalEquipo1) {
                    $ganadorId = $partido->equipo2_id;
                }
                // Si es empate, ganadorId queda null (válido en grupos, inválido en llaves)
                $observaciones = 'Partido único';
            }

            // Caso 2: Dos juegos
            elseif ($cantidadJuegos === 2) {
                $hayPenales = in_array('penales', $tipos);

                if ($hayPenales) {
                    // partido/ida + penales
                    $indexPenales = array_search('penales', $tipos);
                    $penales = $juegos[$indexPenales];

                    if ($penales['juegos_equipo1'] > $penales['juegos_equipo2']) {
                        $ganadorId = $partido->equipo1_id;
                    } else {
                        $ganadorId = $partido->equipo2_id;
                    }

                    $tiempoRegular = $juegos[0];
                    $observaciones = sprintf(
                        'Empate %d-%d, definido por penales %d-%d',
                        $tiempoRegular['juegos_equipo1'],
                        $tiempoRegular['juegos_equipo2'],
                        $penales['juegos_equipo1'],
                        $penales['juegos_equipo2']
                    );
                    $mensaje = 'Resultado cargado. Ganador definido por penales.';
                } else {
                    // ida + vuelta
                    if ($totalEquipo1 > $totalEquipo2) {
                        $ganadorId = $partido->equipo1_id;
                    } elseif ($totalEquipo2 > $totalEquipo1) {
                        $ganadorId = $partido->equipo2_id;
                    }

                    $observaciones = sprintf(
                        'Partido de vuelta, global: %d-%d (Ida: %d-%d, Vuelta: %d-%d)',
                        $totalEquipo1,
                        $totalEquipo2,
                        $juegos[0]['juegos_equipo1'],
                        $juegos[0]['juegos_equipo2'],
                        $juegos[1]['juegos_equipo1'],
                        $juegos[1]['juegos_equipo2']
                    );
                }
            }

            // Caso 3: Tres juegos (ida + vuelta + penales)
            elseif ($cantidadJuegos === 3) {
                $penales = $juegos[2];

                if ($penales['juegos_equipo1'] > $penales['juegos_equipo2']) {
                    $ganadorId = $partido->equipo1_id;
                } else {
                    $ganadorId = $partido->equipo2_id;
                }

                $globalEquipo1 = $juegos[0]['juegos_equipo1'] + $juegos[1]['juegos_equipo1'];
                $globalEquipo2 = $juegos[0]['juegos_equipo2'] + $juegos[1]['juegos_equipo2'];

                $observaciones = sprintf(
                    'Global: %d-%d (Ida: %d-%d, Vuelta: %d-%d), definido por penales %d-%d',
                    $globalEquipo1,
                    $globalEquipo2,
                    $juegos[0]['juegos_equipo1'],
                    $juegos[0]['juegos_equipo2'],
                    $juegos[1]['juegos_equipo1'],
                    $juegos[1]['juegos_equipo2'],
                    $penales['juegos_equipo1'],
                    $penales['juegos_equipo2']
                );
                $mensaje = 'Resultado cargado. Empate en global, ganador definido por penales.';
            }
        }
        // ✅ Para Padel/Tenis: lógica actual (sets)
        else {
            if ($totalEquipo1 > $totalEquipo2) {
                $ganadorId = $partido->equipo1_id;
            } elseif ($totalEquipo2 > $totalEquipo1) {
                $ganadorId = $partido->equipo2_id;
            }
            $observaciones = sprintf('%d sets jugados', $cantidadJuegos);
        }

        return [
            'total_equipo1' => $totalEquipo1,
            'total_equipo2' => $totalEquipo2,
            'ganador_id' => $ganadorId,
            'observaciones' => $observaciones,
            'mensaje' => $mensaje,
        ];
    }
}
