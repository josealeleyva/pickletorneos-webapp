<div class="bg-white rounded-lg shadow-sm p-6">
    @php
        // Buscar la llave de final y verificar si tiene ganador
        $llavesFinal = $llavesPorRonda['Final'] ?? collect();
        $llaveFinal = $llavesFinal->first();
        $campeon = null;
        if ($llaveFinal && $llaveFinal->partido && $llaveFinal->partido->estado === 'finalizado' && $llaveFinal->partido->equipoGanador) {
            $campeon = $llaveFinal->partido->equipoGanador;
        }

        /**
         * Determinar si una llave vacía es un BYE real o está esperando ganador
         *
         * Lógica: Buscar todas las llaves que tienen proxima_llave_id = esta llave
         * Si alguna tiene ambos equipos, entonces está "Esperando" el resultado
         *
         * @param object $llave La llave actual
         * @param string $posicion 'equipo1' o 'equipo2'
         * @return array ['mensaje' => string]
         */
        $determinarEstadoEquipo = function($llave, $posicion) use ($llavesPorRonda) {
            // Si el equipo existe, retornar su nombre
            if ($llave->{$posicion}) {
                return ['mensaje' => $llave->{$posicion}->nombre];
            }

            // Buscar TODAS las llaves que avanzan a esta llave (que tienen proxima_llave_id = $llave->id)
            $llavesQueAvanzanAqui = collect();
            foreach ($llavesPorRonda as $ronda => $llaves) {
                foreach ($llaves as $llaveAnterior) {
                    if ($llaveAnterior->proxima_llave_id == $llave->id) {
                        $llavesQueAvanzanAqui->push($llaveAnterior);
                    }
                }
            }

            // Si no hay llaves que avanzan aquí, es BYE real (primera ronda o sin llaves previas)
            if ($llavesQueAvanzanAqui->isEmpty()) {
                return ['mensaje' => 'BYE'];
            }

            // Ordenar por orden para determinar posiciones
            $llavesQueAvanzanAqui = $llavesQueAvanzanAqui->sortBy('orden')->values();

            // Determinar qué llave anterior alimenta esta posición
            // Primera llave → equipo1, Segunda llave → equipo2
            $llaveAnteriorIndex = ($posicion === 'equipo1') ? 0 : 1;
            $llaveAnterior = $llavesQueAvanzanAqui->get($llaveAnteriorIndex);

            // Si no hay llave para esta posición, es BYE
            if (!$llaveAnterior) {
                return ['mensaje' => 'BYE'];
            }

            // Verificar el estado de la llave anterior:

            // Caso 1: Llave anterior SIN equipos (ambos null) → Verificar recursivamente
            // Esto pasa cuando las llaves se crean pero aún no se definen equipos
            if (!$llaveAnterior->equipo1_id && !$llaveAnterior->equipo2_id) {
                // Verificar si ESA llave anterior también está esperando
                // Si tiene llaves que avanzan a ella, entonces está esperando
                $llavesQueLlenanLaAnterior = collect();
                foreach ($llavesPorRonda as $r => $lls) {
                    foreach ($lls as $ll) {
                        if ($ll->proxima_llave_id == $llaveAnterior->id) {
                            $llavesQueLlenanLaAnterior->push($ll);
                        }
                    }
                }

                // Si la llave anterior tiene llaves que avanzan a ella Y esas llaves tienen equipos
                if ($llavesQueLlenanLaAnterior->isNotEmpty()) {
                    $tieneEquiposDefinidos = $llavesQueLlenanLaAnterior->contains(function($ll) {
                        return $ll->equipo1_id || $ll->equipo2_id;
                    });

                    if ($tieneEquiposDefinidos) {
                        return ['mensaje' => 'Esperando Llave #' . $llaveAnterior->orden];
                    }
                }

                return ['mensaje' => 'BYE'];
            }

            // Caso 2: Llave anterior con solo UN equipo (avance automático) → BYE
            if (($llaveAnterior->equipo1_id && !$llaveAnterior->equipo2_id) ||
                (!$llaveAnterior->equipo1_id && $llaveAnterior->equipo2_id)) {
                return ['mensaje' => 'BYE'];
            }

            // Caso 3: Llave anterior con AMBOS equipos definidos → Esperando resultado
            return ['mensaje' => 'Esperando Llave #' . $llaveAnterior->orden];
        };
    @endphp

    @if($campeon)
        <div class="flex items-center gap-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 px-4 py-2 rounded-lg shadow-lg mb-6">
            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <div>
                <div class="text-xs font-semibold uppercase">Campeón</div>
                <div class="font-bold">{{ $campeon->nombre }}</div>
            </div>
        </div>
    @endif

    @if($llavesPorRonda->isEmpty())
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 text-center">
            <p class="text-yellow-800">No hay llaves generadas para esta categoría.</p>
        </div>
    @else
        <!-- Bracket horizontal (columnas por ronda) -->
        <div class="overflow-x-auto">
            <div class="inline-flex space-x-8 pb-4" style="min-width: 100%;">
                @foreach($rondas as $rondaNombre)
                    @php
                        $llaves = $llavesPorRonda[$rondaNombre] ?? collect();
                    @endphp

                    <div class="flex-shrink-0" style="width: 250px;">
                        <!-- Título de la ronda -->
                        <div class="text-center mb-4">
                            <h3 class="font-bold text-gray-800 text-lg">{{ $rondaNombre }}</h3>
                            <p class="text-xs text-gray-500">{{ $llaves->count() }} {{ $llaves->count() === 1 ? 'partido' : 'partidos' }}</p>
                        </div>

                        <!-- Llaves de la ronda -->
                        <div class="space-y-6">
                            @foreach($llaves as $llave)
                                <div class="bg-white border-2 border-gray-300 rounded-lg overflow-hidden hover:border-blue-400 transition-colors">
                                    <!-- Encabezado de la llave -->
                                    <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                                        <div class="text-xs text-gray-600 space-y-1">
                                            <div class="font-semibold text-gray-700">Llave #{{ $llave->orden }}</div>

                                            @if($llave->partido)
                                                @if($llave->partido->fecha_hora)
                                                    <div class="flex items-center">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                        </svg>
                                                        {{ $llave->partido->fecha_hora->format('d/m/Y H:i') }}
                                                    </div>
                                                @endif

                                                @if($llave->partido->cancha)
                                                    <div class="flex items-center">
                                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                        </svg>
                                                        {{ $llave->partido->cancha->complejo->nombre }}, {{ $llave->partido->cancha->nombre }}
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Equipos -->
                                    <div class="divide-y divide-gray-200">
                                        @if($llave->partido && $llave->partido->estado === 'finalizado' && $llave->partido->juegos->isNotEmpty())
                                            <!-- Vista con juegos en columnas -->
                                            @php
                                                $juegos = $llave->partido->juegos->sortBy('numero_juego');
                                                $esFutbol = $torneo->deporte->esFutbol();
                                            @endphp

                                            @if($esFutbol)
                                                {{-- ✅ Vista para FÚTBOL con labels de tipo --}}
                                                <div class="px-2 py-2 text-xs">
                                                    @foreach($juegos as $juego)
                                                        @php
                                                            $labels = [
                                                                'partido' => 'Partido',
                                                                'ida' => 'Ida',
                                                                'vuelta' => 'Vuelta',
                                                                'penales' => 'Pen.'
                                                            ];
                                                            $label = $labels[$juego->tipo_juego] ?? 'Juego';
                                                            $esPenales = $juego->tipo_juego === 'penales';
                                                        @endphp
                                                        <div class="flex items-center justify-between py-1 {{ $esPenales ? 'bg-yellow-50 -mx-2 px-2 rounded' : '' }}">
                                                            <span class="text-gray-600 {{ $esPenales ? 'font-semibold text-yellow-700' : '' }}">{{ $label }}:</span>
                                                            <span class="font-bold {{ $esPenales ? 'text-yellow-900' : 'text-gray-900' }}">
                                                                {{ $juego->juegos_equipo1 }} - {{ $juego->juegos_equipo2 }}
                                                            </span>
                                                        </div>
                                                    @endforeach

                                                    @if($juegos->count() > 1 && !$juegos->last()->tipo_juego === 'penales')
                                                        {{-- Mostrar global si hay ida+vuelta --}}
                                                        <div class="border-t border-gray-300 mt-1 pt-1 flex items-center justify-between font-semibold text-blue-700">
                                                            <span>Global:</span>
                                                            <span>{{ $llave->partido->sets_equipo1 }} - {{ $llave->partido->sets_equipo2 }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                                <!-- Ganador (equipo completo en verde) -->
                                                @php
                                                    $textoEq1 = $llave->equipo1 ? $llave->equipo1->nombre : $determinarEstadoEquipo($llave, 'equipo1')['mensaje'];
                                                    $textoEq2 = $llave->equipo2 ? $llave->equipo2->nombre : $determinarEstadoEquipo($llave, 'equipo2')['mensaje'];
                                                @endphp
                                                <div class="px-2 py-2 mt-1 {{ $llave->partido->equipo_ganador_id === $llave->equipo1_id ? 'bg-green-100' : '' }}">
                                                    <span class="text-sm font-semibold {{ $llave->partido->equipo_ganador_id === $llave->equipo1_id ? 'text-green-900' : 'text-gray-400' }} italic">
                                                        {{ $textoEq1 }}
                                                    </span>
                                                </div>
                                                <div class="px-2 py-2 {{ $llave->partido->equipo_ganador_id === $llave->equipo2_id ? 'bg-green-100' : '' }}">
                                                    <span class="text-sm font-semibold {{ $llave->partido->equipo_ganador_id === $llave->equipo2_id ? 'text-green-900' : 'text-gray-400' }} italic">
                                                        {{ $textoEq2 }}
                                                    </span>
                                                </div>
                                            @else
                                                {{-- ✅ Vista para PADEL/TENIS (código original) --}}
                                                <!-- Equipo 1 -->
                                                <div class="px-3 py-2 {{ $llave->partido->equipo_ganador_id === $llave->equipo1_id ? 'bg-green-50 font-semibold' : '' }}">
                                                    @if($llave->equipo1)
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="text-sm text-gray-800 flex-shrink-0 min-w-0 truncate">{{ $llave->equipo1->nombre }}</span>
                                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                                @foreach($juegos as $juego)
                                                                    <span class="text-xs font-bold text-gray-900 w-6 text-center border-l border-gray-300 pl-1">{{ $juego->juegos_equipo1 }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipo($llave, 'equipo1')['mensaje'] }}</span>
                                                    @endif
                                                </div>

                                                <!-- Equipo 2 -->
                                                <div class="px-3 py-2 {{ $llave->partido->equipo_ganador_id === $llave->equipo2_id ? 'bg-green-50 font-semibold' : '' }}">
                                                    @if($llave->equipo2)
                                                        <div class="flex items-center justify-between gap-2">
                                                            <span class="text-sm text-gray-800 flex-shrink-0 min-w-0 truncate">{{ $llave->equipo2->nombre }}</span>
                                                            <div class="flex items-center gap-1 flex-shrink-0">
                                                                @foreach($juegos as $juego)
                                                                    <span class="text-xs font-bold text-gray-900 w-6 text-center border-l border-gray-300 pl-1">{{ $juego->juegos_equipo2 }}</span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @else
                                                        <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipo($llave, 'equipo2')['mensaje'] }}</span>
                                                    @endif
                                                </div>
                                            @endif
                                        @else
                                            <!-- Vista normal sin resultados -->
                                            <!-- Equipo 1 -->
                                            <div class="px-3 py-3">
                                                @if($llave->equipo1)
                                                    <span class="text-sm text-gray-800">{{ $llave->equipo1->nombre }}</span>
                                                @else
                                                    <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipo($llave, 'equipo1')['mensaje'] }}</span>
                                                @endif
                                            </div>

                                            <!-- Equipo 2 -->
                                            <div class="px-3 py-3">
                                                @if($llave->equipo2)
                                                    <span class="text-sm text-gray-800">{{ $llave->equipo2->nombre }}</span>
                                                @else
                                                    <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipo($llave, 'equipo2')['mensaje'] }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Estado y acciones -->
                                    <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                                        @if(!$llave->partido)
                                            @if($llave->equipo1 && $llave->equipo2)
                                                <!-- Ambos equipos definidos, se puede programar -->
                                                @can('update', $torneo)
                                                    <button onclick="programarPartidoLlave({{ $llave->id }})"
                                                            class="text-xs text-blue-600 hover:text-blue-800 font-medium">
                                                        + Programar partido
                                                    </button>
                                                @else
                                                    <span class="text-xs text-gray-500">Por programar</span>
                                                @endcan
                                            @elseif($llave->equipo1 || $llave->equipo2)
                                                <!-- Un equipo tiene BYE, avanza automáticamente -->
                                                <span class="text-xs text-green-600">Avance automático (BYE)</span>
                                            @else
                                                <!-- Ambos BYE -->
                                                <span class="text-xs text-gray-400">Esperando ronda anterior</span>
                                            @endif
                                        @elseif($llave->partido->estado === 'programado')
                                            <div class="flex items-center justify-between">
                                                @can('update', $torneo)
                                                    <div class="flex space-x-2">
                                                        @if(in_array($torneo->estado, ['en_curso', 'finalizado']) && $llave->partido->estado !== 'finalizado')
                                                        <button onclick='cargarResultadoLlave({{ $llave->id }}, {{ $llave->partido->id }}, {id: {{ $llave->equipo1->id }}, nombre: "{{ addslashes($llave->equipo1->nombre) }}"}, {id: {{ $llave->equipo2->id }}, nombre: "{{ addslashes($llave->equipo2->nombre) }}"})'
                                                                class="text-xs text-green-600 hover:text-green-800 font-medium">
                                                            Cargar
                                                        </button>
                                                        <button onclick="enviarNotificacionesLlave({{ $llave->id }})"
                                                                class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                                                                title="Enviar notificaciones">
                                                            Notificar
                                                        </button>
                                                        @endif
                                                        <button onclick="programarPartidoLlave({{ $llave->id }}, {{ $llave->partido->id }})"
                                                                class="text-xs text-blue-600 hover:text-blue-800">
                                                            Editar
                                                        </button>
                                                    </div>
                                                @endcan
                                            </div>
                                        @elseif($llave->partido->estado === 'finalizado')
                                            <div class="text-xs text-green-600 font-semibold">Finalizado</div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Información adicional -->
        <!--@if($llavesPorRonda->has('3er Puesto'))
        <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="font-bold text-gray-800 mb-4">Partido por el 3er y 4to Puesto</h3>
                @php
                    $tercerPuesto = $llavesPorRonda['3er Puesto']->first();
                @endphp

                @if($tercerPuesto)
                    <div class="max-w-md">
                        <div class="bg-white border-2 border-gray-300 rounded-lg overflow-hidden">
                            <div class="bg-gray-50 px-3 py-2 border-b border-gray-200">
                                <div class="flex items-center justify-between text-xs text-gray-600">
                                    <span>3er Puesto</span>
                                    @if($tercerPuesto->partido && $tercerPuesto->partido->fecha_hora)
                                        <span>{{ $tercerPuesto->partido->fecha_hora->format('d/m H:i') }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="divide-y divide-gray-200">
                                <div class="px-3 py-3 {{ $tercerPuesto->partido && $tercerPuesto->partido->equipo_ganador_id === $tercerPuesto->equipo1_id ? 'bg-green-50 font-semibold' : '' }}">
                                    @if($tercerPuesto->equipo1)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-800">{{ $tercerPuesto->equipo1->nombre }}</span>
                                            @if($tercerPuesto->partido && $tercerPuesto->partido->estado === 'finalizado')
                                                <span class="text-sm font-bold text-gray-900">{{ $tercerPuesto->partido->sets_equipo1 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 italic">Pendiente semifinal</span>
                                    @endif
                                </div>

                                <div class="px-3 py-3 {{ $tercerPuesto->partido && $tercerPuesto->partido->equipo_ganador_id === $tercerPuesto->equipo2_id ? 'bg-green-50 font-semibold' : '' }}">
                                    @if($tercerPuesto->equipo2)
                                        <div class="flex items-center justify-between">
                                            <span class="text-sm text-gray-800">{{ $tercerPuesto->equipo2->nombre }}</span>
                                            @if($tercerPuesto->partido && $tercerPuesto->partido->estado === 'finalizado')
                                                <span class="text-sm font-bold text-gray-900">{{ $tercerPuesto->partido->sets_equipo2 }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-sm text-gray-500 italic">Pendiente semifinal</span>
                                    @endif
                                </div>
                            </div>

                            <div class="bg-gray-50 px-3 py-2 border-t border-gray-200">
                                @if($tercerPuesto->partido)
                                    <span class="text-xs {{ $tercerPuesto->partido->estado === 'finalizado' ? 'text-green-600' : 'text-blue-600' }}">
                                        {{ ucfirst($tercerPuesto->partido->estado) }}
                                    </span>
                                @else
                                    <span class="text-xs text-gray-500">Esperando semifinales</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        @endif-->
    @endif
</div>
