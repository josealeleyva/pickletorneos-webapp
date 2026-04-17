@extends('layouts.dashboard')

@section('title', 'Llaves - ' . $torneo->nombre)
@section('page-title', 'Llaves de Eliminación')

@section('content')
<div class="max-w-7xl mx-auto space-y-4 sm:space-y-6">
    <!-- Breadcrumb -->
    <nav class="flex text-sm" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('torneos.index') }}" class="text-gray-500 hover:text-gray-700">Torneos</a>
            </li>
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('torneos.show', $torneo) }}" class="text-gray-500 hover:text-gray-700">{{ $torneo->nombre }}</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-700 font-medium">Llaves</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $torneo->nombre }}</h2>
                <p class="text-sm text-gray-600 mt-1">
                    Llaves de Eliminación - {{ $torneo->categorias->count() }} {{ $torneo->categorias->count() === 1 ? 'categoría' : 'categorías' }}
                </p>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex flex-wrap gap-2 sm:gap-3">
        @can('update', $torneo)
            @php
                $partidosProgramados = $torneo->llaves()
                    ->whereHas('partido', function($q) {
                        $q->where('estado', 'programado')->whereNotNull('fecha_hora');
                    })
                    ->count();
            @endphp
            @if($partidosProgramados > 0 && in_array($torneo->estado, ['activo', 'finalizado']))
                <form method="POST" action="{{ route('torneos.llaves.notificar-todos', $torneo) }}" class="inline"
                      onsubmit="return confirm('¿Enviar notificaciones a todos los partidos programados de las llaves?')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        Notificar todos
                    </button>
                </form>
            @endif
            @if($partidosProgramados > 0 && in_array($torneo->estado, ['borrador']))
            <form method="POST" action="{{ route('torneos.llaves.reset', $torneo) }}" class="inline"
                  onsubmit="return confirm('¿Estás seguro de resetear las llaves? Esto eliminará todos los brackets generados.')">
                @csrf
                <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Resetear Llaves
                </button>
            </form>
            @endif
        @endcan

        @if(!empty($llavesPorCategoria))
            <button onclick="copiarLlaves()" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                </svg>
                Copiar Llaves
            </button>
        @endif

        <a href="{{ route('torneos.show', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Torneo
        </a>
    </div>

    @php
        // Verificar si todas las categorías tienen campeón
        $todasCategoriasTerminadas = true;
        $campeonesPorCategoria = [];

        foreach($llavesPorCategoria as $catId => $data) {
            $llavesFinal = $data['llaves_por_ronda']['Final'] ?? collect();
            $llaveFinal = $llavesFinal->first();

            if ($llaveFinal && $llaveFinal->partido && $llaveFinal->partido->estado === 'finalizado' && $llaveFinal->partido->equipoGanador) {
                $campeonesPorCategoria[$catId] = $llaveFinal->partido->equipoGanador;
            } else {
                $todasCategoriasTerminadas = false;
            }
        }
    @endphp

    @if($todasCategoriasTerminadas && $torneo->estado !== 'finalizado' && count($campeonesPorCategoria) > 0)
        <div class="bg-green-50 border-2 border-green-500 rounded-lg p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-green-800 mb-2">🎉 ¡Todas las categorías han finalizado!</h3>
                    <p class="text-green-700">Todas las finales se han jugado. Puedes finalizar el torneo ahora.</p>
                </div>
                @can('update', $torneo)
                    <form method="POST" action="{{ route('torneos.finalizar') }}" class="inline"
                          onsubmit="return confirm('¿Estás seguro de finalizar el torneo? Esta acción cambiará el estado del torneo a Finalizado.')">
                        @csrf
                        <input type="hidden" name="torneo_id" value="{{ $torneo->id }}">
                        <button type="submit" class="px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-lg">
                            Finalizar Torneo
                        </button>
                    </form>
                @endcan
            </div>
        </div>
    @endif

    <!-- Tabs por categoría (Desktop) -->
    <div class="hidden sm:block">
        <div class="bg-white rounded-lg shadow-sm border-b border-gray-200">
            <nav class="flex overflow-x-auto" aria-label="Categorías">
                @foreach($llavesPorCategoria as $catId => $data)
                    <button type="button"
                            class="tab-button whitespace-nowrap py-4 px-6 border-b-2 font-medium text-sm {{ $loop->first ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                            data-tab="bracket-{{ $catId }}">
                        {{ $data['categoria']->nombre }}
                    </button>
                @endforeach
            </nav>
        </div>

        <div class="mt-6">
            @foreach($llavesPorCategoria as $catId => $data)
                <div class="tab-content {{ $loop->first ? '' : 'hidden' }}" id="bracket-{{ $catId }}">
                    @include('torneos.llaves.partials.bracket-categoria', [
                        'categoria' => $data['categoria'],
                        'llavesPorRonda' => $data['llaves_por_ronda'],
                        'rondas' => $data['rondas'],
                        'torneo' => $torneo,
                        'canchas' => $canchas
                    ])
                </div>
            @endforeach
        </div>
    </div>

    <!-- Mobile: Selector dropdown + Accordion -->
    <div class="sm:hidden">
        <div class="bg-white rounded-lg shadow-sm">
            <div class="p-4 border-b border-gray-200">
                <select id="categoria-filter-select-mobile" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    @foreach($llavesPorCategoria as $catId => $data)
                        <option value="categoria-mobile-{{ $catId }}">{{ $data['categoria']->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="p-4">
                @foreach($llavesPorCategoria as $catId => $data)
                    <div class="categoria-content-mobile {{ $loop->first ? '' : 'hidden' }}" data-categoria="categoria-mobile-{{ $catId }}">
                        @include('torneos.llaves.partials.bracket-categoria-mobile', [
                            'categoria' => $data['categoria'],
                            'llavesPorRonda' => $data['llaves_por_ronda'],
                            'rondas' => $data['rondas'],
                            'torneo' => $torneo,
                            'canchas' => $canchas
                        ])
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Modal Programar Partido de Llave -->
<div id="modal-programar-llave" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">Programar Partido</h3>
        </div>
        <form id="form-programar-llave" onsubmit="event.preventDefault(); submitProgramarLlave();">
            <input type="hidden" id="programar-llave-id" name="llave_id">
            <div class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora</label>
                    <input type="datetime-local" id="programar-fecha-hora" name="fecha_hora" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cancha (opcional)</label>
                    <select id="programar-cancha-id" name="cancha_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Sin asignar</option>
                        @foreach($canchas as $cancha)
                            <option value="{{ $cancha->id }}">{{ $cancha->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                <button type="button" onclick="cerrarModalProgramarLlave()"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300">
                    Cancelar
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                    Programar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Cargar Resultado de Llave -->
<div id="modal-resultado-llave" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-xl w-full max-h-[90vh] overflow-y-auto">
        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-800">Cargar Resultado</h3>
            <button onclick="cerrarModalResultadoLlave()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <div class="p-6">
            <input type="hidden" id="resultado-llave-id" name="llave_id">
            <input type="hidden" id="resultado-partido-id" name="partido_id">

            <div class="mb-4 text-center bg-gray-50 p-3 rounded-lg">
                <span id="llave-equipo1-nombre" class="font-semibold text-gray-900"></span>
                <span class="mx-2 text-gray-500">vs</span>
                <span id="llave-equipo2-nombre" class="font-semibold text-gray-900"></span>
            </div>

            <!-- Puntos totales (acumulados) -->
            <div class="mb-4 bg-blue-50 p-4 rounded-lg">
                <div class="text-center mb-2 text-sm text-gray-600">{{ $torneo->deporte->esFutbol() ? 'Goles' : 'Games' }} Acumulados</div>
                <div class="flex items-center justify-center gap-4">
                    <div class="text-center">
                        <div id="llave-puntos-acum-equipo1" class="text-3xl font-bold text-blue-600">0</div>
                        <div class="text-xs text-gray-500" id="llave-puntos-label1"></div>
                    </div>
                    <div class="text-2xl text-gray-400">-</div>
                    <div class="text-center">
                        <div id="llave-puntos-acum-equipo2" class="text-3xl font-bold text-blue-600">0</div>
                        <div class="text-xs text-gray-500" id="llave-puntos-label2"></div>
                    </div>
                </div>
            </div>

            <!-- Juegos cargados -->
            <div id="llave-juegos-lista" class="mb-4 space-y-2">
                <!-- Los juegos se agregan dinámicamente aquí -->
            </div>

            @if($torneo->deporte->esFutbol())
                {{-- ✅ Selector de tipo cuando hay empate (solo fútbol) --}}
                <div id="selector-tipo-siguiente-juego" class="hidden mb-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-sm text-yellow-800 mb-3 font-medium">
                        ⚠️ El partido terminó en empate. ¿Cómo deseas continuar?
                    </p>
                    <div class="space-y-2">
                        <label class="flex items-center cursor-pointer hover:bg-yellow-100 p-2 rounded">
                            <input type="radio" name="tipo_siguiente_juego" value="vuelta" class="mr-3 w-4 h-4">
                            <div>
                                <span class="text-sm font-medium">Partido de Vuelta</span>
                                <p class="text-xs text-gray-600">Se sumará al global para determinar el ganador</p>
                            </div>
                        </label>
                        <label class="flex items-center cursor-pointer hover:bg-yellow-100 p-2 rounded">
                            <input type="radio" name="tipo_siguiente_juego" value="penales" class="mr-3 w-4 h-4">
                            <div>
                                <span class="text-sm font-medium">Penales (Definición final)</span>
                                <p class="text-xs text-gray-600">Define el ganador mediante penales</p>
                            </div>
                        </label>
                    </div>
                </div>

                {{-- Botón agregar partido de vuelta --}}
                <div id="btn-agregar-vuelta" class="hidden mb-4">
                    <button type="button" onclick="prepararAgregarJuegoTipo('vuelta')"
                            class="w-full px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 border border-blue-300 font-medium">
                        + Agregar Partido de Vuelta
                    </button>
                </div>

                {{-- Botón agregar penales --}}
                <div id="btn-agregar-penales" class="hidden mb-4">
                    <button type="button" onclick="prepararAgregarJuegoTipo('penales')"
                            class="w-full px-4 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 border border-yellow-300 font-medium">
                        ⚽ Definir por Penales
                    </button>
                </div>
            @endif

            <!-- Formulario para agregar juego -->
            <form id="formAgregarJuegoLlave" onsubmit="agregarJuegoLlave(event)" class="mb-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <div class="text-sm font-medium text-gray-700 mb-2" id="titulo-agregar-juego">
                        Agregar {{ $torneo->deporte->esFutbol() ? 'Resultado' : 'Set' }}
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-3">
                        <div>
                            <label for="llave-juegos-equipo1" class="block text-xs text-gray-600 mb-1" id="llave-label-juego1"></label>
                            <input
                                type="number"
                                id="llave-juegos-equipo1"
                                name="juegos_equipo1"
                                min="0"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-xl font-bold">
                        </div>
                        <div>
                            <label for="llave-juegos-equipo2" class="block text-xs text-gray-600 mb-1" id="llave-label-juego2"></label>
                            <input
                                type="number"
                                id="llave-juegos-equipo2"
                                name="juegos_equipo2"
                                min="0"
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-xl font-bold">
                        </div>
                    </div>
                    <button
                        type="submit"
                        id="btn-agregar-juego"
                        class="w-full px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition text-sm">
                        Agregar
                    </button>
                </div>
            </form>

            <!-- Botones -->
            <div class="flex gap-3">
                <button
                    type="button"
                    onclick="cerrarModalResultadoLlave()"
                    class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                    Cancelar
                </button>
                <button
                    type="button"
                    onclick="finalizarPartidoLlave()"
                    id="btnFinalizarLlave"
                    disabled
                    class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                    Finalizar Partido
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Tabs desktop
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.tab;

            // Actualizar botones
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-blue-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-blue-600');

            // Mostrar contenido
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            document.getElementById(targetId).classList.remove('hidden');
        });
    });

    // Accordion mobile
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.accordion;
            const content = document.getElementById(targetId);
            const svg = this.querySelector('svg');

            content.classList.toggle('hidden');
            svg.classList.toggle('rotate-180');
        });
    });

    // Programar Partido de Llave
    function programarPartidoLlave(llaveId, partidoId = null) {
        document.getElementById('programar-llave-id').value = llaveId;

        // Si hay partidoId, cargar datos existentes
        if (partidoId) {
            // Aquí podrías cargar los datos del partido existente si es necesario
        }

        document.getElementById('modal-programar-llave').classList.remove('hidden');
    }

    function cerrarModalProgramarLlave() {
        document.getElementById('modal-programar-llave').classList.add('hidden');
        document.getElementById('form-programar-llave').reset();
    }

    function submitProgramarLlave() {
        const llaveId = document.getElementById('programar-llave-id').value;
        const fechaHora = document.getElementById('programar-fecha-hora').value;
        const canchaId = document.getElementById('programar-cancha-id').value;

        fetch(`/torneos/{{ $torneo->id }}/llaves/${llaveId}/programar`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                fecha_hora: fechaHora,
                cancha_id: canchaId || null
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                cerrarModalProgramarLlave();
                location.reload();
            } else {
                alert(data.message || 'Error al programar el partido');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al programar el partido');
        });
    }

    // Variables globales para el modal de resultado de llave
    let juegosDelPartidoLlave = [];
    let puntosEquipo1Llave = 0;
    let puntosEquipo2Llave = 0;
    let nombreEquipo1Llave = '';
    let nombreEquipo2Llave = '';
    const esFutbol = {{ $torneo->deporte->esFutbol() ? 'true' : 'false' }}; // ✅ NUEVO
    let tipoProximoJuego = 'partido'; // ✅ NUEVO: partido, ida, vuelta, penales

    // Cargar Resultado de Llave
    function cargarResultadoLlave(llaveId, partidoId, equipo1, equipo2) {
        // Resetear variables
        juegosDelPartidoLlave = [];
        puntosEquipo1Llave = 0;
        puntosEquipo2Llave = 0;
        nombreEquipo1Llave = equipo1.nombre;
        nombreEquipo2Llave = equipo2.nombre;
        tipoProximoJuego = 'partido'; // ✅ NUEVO

        // Configurar modal
        document.getElementById('resultado-llave-id').value = llaveId;
        document.getElementById('resultado-partido-id').value = partidoId;
        document.getElementById('llave-equipo1-nombre').textContent = equipo1.nombre;
        document.getElementById('llave-equipo2-nombre').textContent = equipo2.nombre;
        document.getElementById('llave-puntos-label1').textContent = equipo1.nombre;
        document.getElementById('llave-puntos-label2').textContent = equipo2.nombre;

        const labelPrefix = esFutbol ? 'Goles' : 'Juegos';
        document.getElementById('llave-label-juego1').textContent = labelPrefix + ' ' + equipo1.nombre;
        document.getElementById('llave-label-juego2').textContent = labelPrefix + ' ' + equipo2.nombre;

        // Limpiar inputs y lista
        document.getElementById('llave-juegos-equipo1').value = '';
        document.getElementById('llave-juegos-equipo2').value = '';
        document.getElementById('llave-juegos-lista').innerHTML = '';

        // ✅ NUEVO: Ocultar controles de fútbol
        if (esFutbol) {
            document.getElementById('selector-tipo-siguiente-juego')?.classList.add('hidden');
            document.getElementById('btn-agregar-vuelta')?.classList.add('hidden');
            document.getElementById('btn-agregar-penales')?.classList.add('hidden');
            document.querySelectorAll('input[name="tipo_siguiente_juego"]').forEach(r => r.checked = false);
        }

        // Actualizar puntos acumulados
        actualizarPuntosAcumuladosLlave();

        // Deshabilitar botón finalizar
        document.getElementById('btnFinalizarLlave').disabled = true;

        document.getElementById('modal-resultado-llave').classList.remove('hidden');
    }

    function cerrarModalResultadoLlave() {
        document.getElementById('modal-resultado-llave').classList.add('hidden');
    }

    function actualizarPuntosAcumuladosLlave() {
        document.getElementById('llave-puntos-acum-equipo1').textContent = puntosEquipo1Llave;
        document.getElementById('llave-puntos-acum-equipo2').textContent = puntosEquipo2Llave;
    }

    function agregarJuegoLlave(event) {
        event.preventDefault();

        const juegos1 = parseInt(document.getElementById('llave-juegos-equipo1').value);
        const juegos2 = parseInt(document.getElementById('llave-juegos-equipo2').value);

        // ✅ NUEVO: Validar penales empatados (solo fútbol)
        if (esFutbol && tipoProximoJuego === 'penales' && juegos1 === juegos2) {
            alert('❌ Los penales no pueden terminar empatados. Debe haber un ganador.');
            return;
        }

        // ✅ NUEVO: Limitar a 3 juegos en fútbol
        if (esFutbol && juegosDelPartidoLlave.length >= 3) {
            alert('❌ No se pueden agregar más de 3 juegos (Ida, Vuelta, Penales)');
            return;
        }

        // ✅ NUEVO: Si es fútbol y es vuelta, cambiar el primer juego de "partido" a "ida"
        if (esFutbol && tipoProximoJuego === 'vuelta' && juegosDelPartidoLlave.length === 1) {
            if (juegosDelPartidoLlave[0].tipo_juego === 'partido') {
                juegosDelPartidoLlave[0].tipo_juego = 'ida';
                // Recargar la lista visual
                document.getElementById('llave-juegos-lista').innerHTML = '';
                agregarJuegoAListaLlave(juegosDelPartidoLlave[0].juegos_equipo1, juegosDelPartidoLlave[0].juegos_equipo2, 'ida');
            }
        }

        // Sumar puntos
        puntosEquipo1Llave += juegos1;
        puntosEquipo2Llave += juegos2;

        // Agregar juego a la lista
        const nuevoJuego = {
            juegos_equipo1: juegos1,
            juegos_equipo2: juegos2
        };

        // ✅ NUEVO: Agregar tipo_juego para fútbol
        if (esFutbol) {
            nuevoJuego.tipo_juego = tipoProximoJuego;
        }

        juegosDelPartidoLlave.push(nuevoJuego);

        // Actualizar UI
        actualizarPuntosAcumuladosLlave();
        agregarJuegoAListaLlave(juegos1, juegos2, tipoProximoJuego);

        // Limpiar inputs
        document.getElementById('llave-juegos-equipo1').value = '';
        document.getElementById('llave-juegos-equipo2').value = '';

        // ✅ NUEVO: Actualizar controles de fútbol
        if (esFutbol) {
            actualizarControlesFutbol();
            // Resetear tipo para próximo juego
            tipoProximoJuego = 'partido';
        }

        // Habilitar botón finalizar
        document.getElementById('btnFinalizarLlave').disabled = false;
    }

    function agregarJuegoAListaLlave(juegos1, juegos2, tipo = 'set') {
        // ✅ NUEVO: Determinar label según tipo (fútbol)
        let labelJuego = 'Juego ' + juegosDelPartidoLlave.length;
        let colorBorde = 'border-gray-200';
        let colorTexto = 'text-gray-500';

        if (esFutbol) {
            const labels = {
                'partido': 'Partido',
                'ida': 'Ida',
                'vuelta': 'Vuelta',
                'penales': 'Penales'
            };
            labelJuego = labels[tipo] || 'Partido';

            if (tipo === 'penales') {
                colorBorde = 'border-yellow-400';
                colorTexto = 'text-yellow-700';
            } else if (tipo === 'vuelta') {
                colorBorde = 'border-blue-400';
                colorTexto = 'text-blue-700';
            }
        } else {
            labelJuego = 'Set ' + juegosDelPartidoLlave.length;
        }

        const juegoHtml = `
            <div class="bg-white border ${colorBorde} rounded-lg p-3 flex items-center justify-between">
                <div class="flex items-center gap-4 flex-1">
                    <span class="text-sm font-medium ${colorTexto}">${labelJuego}</span>
                    <span class="font-bold text-lg">${juegos1} - ${juegos2}</span>
                </div>
                <button onclick="eliminarJuegoLlave(${juegosDelPartidoLlave.length - 1})" type="button" class="text-red-600 hover:text-red-800 p-1">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        document.getElementById('llave-juegos-lista').insertAdjacentHTML('beforeend', juegoHtml);
    }

    function eliminarJuegoLlave(index) {
        const juego = juegosDelPartidoLlave[index];

        // Restar puntos
        puntosEquipo1Llave -= juego.juegos_equipo1;
        puntosEquipo2Llave -= juego.juegos_equipo2;

        // Eliminar juego del array
        juegosDelPartidoLlave.splice(index, 1);

        // Recargar lista
        document.getElementById('llave-juegos-lista').innerHTML = '';
        juegosDelPartidoLlave.forEach((j, i) => {
            agregarJuegoAListaLlave(j.juegos_equipo1, j.juegos_equipo2, j.tipo_juego || 'set');
        });

        // Actualizar puntos acumulados
        actualizarPuntosAcumuladosLlave();

        // ✅ NUEVO: Actualizar controles de fútbol
        if (esFutbol) {
            actualizarControlesFutbol();
        }

        // Deshabilitar botón si no hay juegos
        if (juegosDelPartidoLlave.length === 0) {
            document.getElementById('btnFinalizarLlave').disabled = true;
        }
    }

    // ✅ NUEVO: Preparar para agregar juego con tipo específico (fútbol)
    function prepararAgregarJuegoTipo(tipo) {
        tipoProximoJuego = tipo;

        // Actualizar título del formulario
        const titulos = {
            'ida': 'Agregar Partido de Ida',
            'vuelta': 'Agregar Partido de Vuelta',
            'penales': 'Agregar Penales'
        };
        document.getElementById('titulo-agregar-juego').textContent = titulos[tipo] || 'Agregar Resultado';

        // Ocultar selector y botones
        document.getElementById('selector-tipo-siguiente-juego')?.classList.add('hidden');
        document.getElementById('btn-agregar-vuelta')?.classList.add('hidden');
        document.getElementById('btn-agregar-penales')?.classList.add('hidden');

        // MOSTRAR el formulario
        document.getElementById('formAgregarJuegoLlave')?.classList.remove('hidden');

        // Enfocar en el primer input
        document.getElementById('llave-juegos-equipo1').focus();
    }

    // ✅ NUEVO: Actualizar controles según estado actual (fútbol)
    function actualizarControlesFutbol() {
        const cantJuegos = juegosDelPartidoLlave.length;
        const selectorTipo = document.getElementById('selector-tipo-siguiente-juego');
        const btnVuelta = document.getElementById('btn-agregar-vuelta');
        const btnPenales = document.getElementById('btn-agregar-penales');
        const formAgregar = document.getElementById('formAgregarJuegoLlave');

        // Reset
        selectorTipo?.classList.add('hidden');
        btnVuelta?.classList.add('hidden');
        btnPenales?.classList.add('hidden');
        document.querySelectorAll('input[name="tipo_siguiente_juego"]').forEach(r => r.checked = false);

        if (cantJuegos === 0) {
            // No hay juegos, mostrar formulario normal
            formAgregar.classList.remove('hidden');
            document.getElementById('titulo-agregar-juego').textContent = 'Agregar Resultado';
        } else if (cantJuegos === 1) {
            const juego1 = juegosDelPartidoLlave[0];
            const hayEmpate = juego1.juegos_equipo1 === juego1.juegos_equipo2;

            if (hayEmpate) {
                // Empate: mostrar selector (vuelta o penales)
                selectorTipo?.classList.remove('hidden');
                formAgregar.classList.add('hidden');

                // Listener para radio buttons
                document.querySelectorAll('input[name="tipo_siguiente_juego"]').forEach(radio => {
                    radio.addEventListener('change', function() {
                        if (this.checked) {
                            prepararAgregarJuegoTipo(this.value);
                            formAgregar.classList.remove('hidden');
                        }
                    });
                });
            } else {
                // Hay ganador: permitir agregar vuelta opcional
                btnVuelta?.classList.remove('hidden');
                formAgregar.classList.add('hidden');
            }
        } else if (cantJuegos === 2) {
            const juego2 = juegosDelPartidoLlave[1];

            if (juego2.tipo_juego === 'penales') {
                // Ya se definió por penales, no agregar más
                formAgregar.classList.add('hidden');
            } else {
                // Es partido de vuelta, verificar global
                const totalE1 = juegosDelPartidoLlave.reduce((sum, j) => sum + j.juegos_equipo1, 0);
                const totalE2 = juegosDelPartidoLlave.reduce((sum, j) => sum + j.juegos_equipo2, 0);

                if (totalE1 === totalE2) {
                    // Empate global: permitir penales
                    btnPenales?.classList.remove('hidden');
                    formAgregar.classList.add('hidden');
                } else {
                    // Hay ganador global, no agregar más
                    formAgregar.classList.add('hidden');
                }
            }
        } else {
            // 3 juegos o más: no permitir agregar más
            formAgregar.classList.add('hidden');
        }
    }

    // ✅ NUEVO: Listener para radio buttons (ejecutar una vez al cargar)
    document.addEventListener('DOMContentLoaded', function() {
        if (esFutbol) {
            document.querySelectorAll('input[name="tipo_siguiente_juego"]').forEach(radio => {
                radio.addEventListener('change', function() {
                    if (this.checked) {
                        prepararAgregarJuegoTipo(this.value);
                        document.getElementById('formAgregarJuegoLlave').classList.remove('hidden');
                    }
                });
            });
        }
    });

    async function finalizarPartidoLlave() {
        if (juegosDelPartidoLlave.length === 0) {
            alert('Debes agregar al menos un juego.');
            return;
        }

        // Confirmar antes de finalizar
        if (!confirm('⚠️ ¿Estás seguro de finalizar este partido?\n\nPor favor verifica que los resultados sean correctos antes de continuar. No se podrán hacer modificaciones luego.')) {
            return;
        }

        const llaveId = document.getElementById('resultado-llave-id').value;

        try {
            const response = await fetch(`/torneos/{{ $torneo->id }}/llaves/${llaveId}/resultado`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    juegos: juegosDelPartidoLlave
                })
            });

            const data = await response.json();

            if (response.ok && data.success) {
                window.location.reload();
            } else {
                alert(data.message || 'Error al cargar el resultado');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el resultado. Por favor intenta nuevamente.');
        }
    }

    // Enviar notificaciones de llave
    async function enviarNotificacionesLlave(llaveId) {
        if (!confirm('¿Deseas enviar notificaciones por email a todos los jugadores de este partido?')) {
            return;
        }

        try {
            const response = await fetch(`/torneos/{{ $torneo->id }}/llaves/notificar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    llave_id: llaveId
                })
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message || 'Notificaciones enviadas exitosamente');
            } else {
                alert(data.error || 'Error al enviar las notificaciones');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al enviar las notificaciones. Por favor intenta nuevamente.');
        }
    }

    // Enviar notificaciones a todos los partidos programados de llaves
    async function enviarNotificacionesTodosLlaves() {
        if (!confirm('¿Enviar notificaciones a todos los partidos programados de las llaves?')) {
            return;
        }

        try {
            const response = await fetch(`/torneos/{{ $torneo->id }}/llaves/notificar-todos`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            });

            const data = await response.json();

            if (data.success) {
                alert(data.message || 'Notificaciones enviadas exitosamente');
                window.location.reload();
            } else {
                alert(data.error || 'Error al enviar las notificaciones');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al enviar las notificaciones. Por favor intenta nuevamente.');
        }
    }

    // Copiar llaves para WhatsApp
    async function copiarLlaves() {
        const torneoNombre = "{{ $torneo->nombre }}";
        const complejoNombre = "{{ $torneo->complejo->nombre }}";

        let textoLlaves = `🏆 *LLAVES - ${torneoNombre}*\n`;
        textoLlaves += `📍 ${complejoNombre}\n`;

        // Iterar sobre cada categoría visible
        const categoriasVisibles = document.querySelectorAll('.tab-content:not(.hidden)');

        categoriasVisibles.forEach(categoriaDiv => {
            const categoriaNombre = categoriaDiv.querySelector('.bg-white.rounded-lg')?.querySelector('h3')?.textContent || '';

            if (categoriaNombre.trim() && categoriaNombre.includes('Campeón')) {
                // Extraer nombre del campeón si existe
                const campeonDiv = categoriaDiv.querySelector('.bg-gradient-to-r.from-yellow-400');
                if (campeonDiv) {
                    const campeonNombre = campeonDiv.querySelector('.font-bold')?.textContent.trim();
                    if (campeonNombre) {
                        textoLlaves += `\n🏅 *${categoriaNombre}* - 🏆 Campeón: ${campeonNombre}\n`;
                    }
                }
            }

            // Obtener nombre de la categoría del tab activo
            const activeTab = document.querySelector('.tab-button.border-indigo-600');
            const catNombre = activeTab?.textContent.trim() || '';

            if (catNombre) {
                textoLlaves += `\n🏅 *${catNombre}*\n`;
            }

            // Buscar las rondas dentro de esta categoría
            const rondasContainer = categoriaDiv.querySelector('.overflow-x-auto .inline-flex');
            if (!rondasContainer) return;

            const rondas = rondasContainer.querySelectorAll(':scope > div');

            rondas.forEach(rondaDiv => {
                const rondaNombre = rondaDiv.querySelector('h3')?.textContent.trim();
                if (!rondaNombre) return;

                // Obtener todas las llaves de esta ronda
                const llaves = rondaDiv.querySelectorAll('.space-y-6 > div');

                llaves.forEach(llaveDiv => {
                    // Número de llave
                    const llaveNumero = llaveDiv.querySelector('.font-semibold.text-gray-700')?.textContent.trim() || '';

                    // Fecha, hora y cancha desde el encabezado
                    let fechaHora = '';
                    let cancha = '';
                    const encabezadoDiv = llaveDiv.querySelector('.bg-gray-50.px-3.py-2.border-b');
                    if (encabezadoDiv) {
                        encabezadoDiv.querySelectorAll('.flex.items-center').forEach(detalle => {
                            const svg = detalle.querySelector('svg');
                            if (!svg) return;
                            const clone = detalle.cloneNode(true);
                            clone.querySelector('svg')?.remove();
                            const texto = clone.textContent.trim();
                            const paths = svg.querySelectorAll('path');
                            let esFecha = false, esCancha = false;
                            paths.forEach(path => {
                                const d = path.getAttribute('d') || '';
                                if (d.includes('M8 7V3')) esFecha = true;
                                if (d.includes('M17.657')) esCancha = true;
                            });
                            if (esFecha && texto) fechaHora = texto;
                            else if (esCancha && texto) cancha = texto;
                        });
                    }

                    // Equipos
                    const equiposDiv = llaveDiv.querySelector('.divide-y.divide-gray-200');
                    let equipo1 = '', equipo2 = '', resultado = '';
                    if (equiposDiv) {
                        const equiposDivs = equiposDiv.querySelectorAll(':scope > div');
                        if (equiposDivs[0]) {
                            equipo1 = equiposDivs[0].querySelector('.text-sm.text-gray-800, .text-sm.text-gray-400')?.textContent.trim() || '';
                            const juegos = equiposDivs[0].querySelectorAll('.text-xs.font-bold.text-gray-900');
                            if (juegos.length > 0) resultado = Array.from(juegos).map(j => j.textContent.trim()).join(' ');
                        }
                        if (equiposDivs[1]) {
                            equipo2 = equiposDivs[1].querySelector('.text-sm.text-gray-800, .text-sm.text-gray-400')?.textContent.trim() || '';
                        }
                    }

                    // Omitir si no está programado o si algún equipo es "Esperando"
                    const esEsperando = str => str.toLowerCase().startsWith('esperando');
                    if (!fechaHora || esEsperando(equipo1) || esEsperando(equipo2)) return;

                    // Acortar fecha: quitar el año (dd/mm/yyyy hh:mm → dd/mm hh:mm)
                    const fechaCorta = fechaHora.replace(/(\d{2}\/\d{2})\/\d{4}/, '$1');

                    // Línea compacta: ⚔️ Ronda | Llave #N | dd/mm hh:mm cancha | eq1 vs eq2
                    let linea = `⚔️ ${rondaNombre} | ${llaveNumero} | ${fechaCorta}`;
                    if (cancha) linea += ` ${cancha}`;
                    linea += ` | ${equipo1} vs ${equipo2}`;
                    if (resultado) linea += ` (${resultado})`;
                    textoLlaves += linea + '\n';
                });
            });

            // Buscar partido por el 3er puesto si existe
            const tercerPuestoDiv = categoriaDiv.querySelector('.mt-8.pt-6');
            if (tercerPuestoDiv) {
                const titulo = tercerPuestoDiv.querySelector('h3')?.textContent.trim();
                if (titulo) {
                    textoLlaves += `\n🥉 *${titulo}*\n\n`;

                    const equiposDivs = tercerPuestoDiv.querySelectorAll('.divide-y.divide-gray-200 > div');
                    if (equiposDivs.length >= 2) {
                        const eq1 = equiposDivs[0].querySelector('.text-sm.text-gray-800')?.textContent.trim() || 'Pendiente';
                        const eq2 = equiposDivs[1].querySelector('.text-sm.text-gray-800')?.textContent.trim() || 'Pendiente';
                        textoLlaves += `${eq1} vs ${eq2}\n`;
                    }
                }
            }
        });

        //textoLlaves += `\n🤖 Generated with Claude Code`;

        // Copiar al portapapeles
        try {
            await navigator.clipboard.writeText(textoLlaves);
            alert('✅ Llaves copiadas al portapapeles!\nAhora puedes pegarlo en WhatsApp.');
        } catch (err) {
            // Fallback
            const textarea = document.createElement('textarea');
            textarea.value = textoLlaves;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                alert('✅ Llaves copiadas al portapapeles!\nAhora puedes pegarlo en WhatsApp.');
            } catch (e) {
                alert('❌ No se pudo copiar automáticamente.');
            }
            document.body.removeChild(textarea);
        }
    }

    // Tab switching (Desktop)
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;

            // Update active tab button
            tabButtons.forEach(btn => {
                btn.classList.remove('border-indigo-600', 'text-indigo-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-indigo-600', 'text-indigo-600');

            // Show/hide tab content
            tabContents.forEach(content => {
                if (content.id === targetTab) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        });
    });

    // Mobile: Select dropdown for category switching
    const categoriaFilterSelectMobile = document.getElementById('categoria-filter-select-mobile');
    const categoriaContentsMobile = document.querySelectorAll('.categoria-content-mobile');

    if (categoriaFilterSelectMobile) {
        categoriaFilterSelectMobile.addEventListener('change', function() {
            const categoriaId = this.value;

            categoriaContentsMobile.forEach(content => {
                if (content.dataset.categoria === categoriaId) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        });
    }

    // Accordion behavior for rondas (mobile)
    const rondaButtons = document.querySelectorAll('.ronda-button');

    rondaButtons.forEach(button => {
        button.addEventListener('click', function() {
            const rondaId = this.dataset.ronda;
            const rondaContent = document.getElementById(rondaId);
            const svg = this.querySelector('svg');

            // Toggle visibility
            rondaContent.classList.toggle('hidden');

            // Rotate arrow
            if (rondaContent.classList.contains('hidden')) {
                svg.style.transform = 'rotate(0deg)';
            } else {
                svg.style.transform = 'rotate(180deg)';
            }
        });
    });
</script>
@endpush
@endsection
