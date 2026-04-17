@extends('layouts.dashboard')

@section('title', 'Agregar Equipo - ' . $torneo->nombre)
@section('page-title', 'Agregar Equipo')

@push('styles')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- jQuery UI CSS (solo para autocomplete en Fútbol) -->
@if($torneo->deporte->requiereNombreEquipo())
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
@endif
<style>
    .select2-container--default .select2-selection--multiple {
        border-color: #d1d5db;
        border-radius: 0.5rem;
        min-height: 42px;
        padding: 4px;
    }
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #6366f1;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #eef2ff !important;
        border: 1px solid #c7d2fe !important;
        border-radius: 0.375rem !important;
        padding: 5px 10px 5px 30px !important;
        color: #4338ca !important;
        font-weight: 500 !important;
        margin: 4px 4px 4px 0 !important;
        position: relative !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #4338ca !important;
        font-size: 18px !important;
        font-weight: bold !important;
        position: absolute !important;
        left: 8px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        margin: 0 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
        color: #dc2626 !important;
        background: transparent !important;
    }
    .select2-search__field {
        margin-top: 4px;
    }
    .select2-container {
        width: 100% !important;
    }
    .select2-results__option {
        padding: 8px 12px;
    }
    .select2-results__option--highlighted {
        background-color: #6366f1 !important;
    }
    .select2-results__option[aria-disabled="true"] {
        color: #9ca3af !important;
        background-color: #f3f4f6 !important;
        cursor: not-allowed !important;
        opacity: 0.6 !important;
    }
    .select2-results__option[aria-disabled="true"]:hover {
        background-color: #f3f4f6 !important;
    }

    /* Estilos para autocomplete de jQuery UI */
    .ui-autocomplete {
        max-height: 300px;
        overflow-y: auto;
        overflow-x: hidden;
        z-index: 9999 !important;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    }
    .ui-menu-item {
        font-size: 14px;
        padding: 2px 0;
    }
    .ui-menu-item-wrapper {
        padding: 10px 12px;
        border-left: 3px solid transparent;
    }
    .ui-menu-item-wrapper:hover,
    .ui-state-active {
        background-color: #eef2ff !important;
        border-left-color: #6366f1 !important;
        color: #1e293b !important;
        border: none;
        margin: 0;
    }
    .equipo-sugerido {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .equipo-nombre {
        font-weight: 600;
        color: #1e293b;
    }
    .equipo-info {
        font-size: 12px;
        color: #64748b;
        margin-left: 8px;
    }
</style>
@endpush

@section('content')
<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6">
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
            <li>
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <a href="{{ route('torneos.equipos.index', $torneo) }}" class="text-gray-500 hover:text-gray-700">Participantes</a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <svg class="w-4 h-4 text-gray-400 mx-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-gray-700 font-medium">Agregar Equipo</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header -->
    <div class="bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg p-4 sm:p-6 text-white">
        <h2 class="text-xl sm:text-2xl font-bold">{{ $torneo->nombre }}</h2>
        <p class="text-sm mt-1 text-indigo-100">{{ $torneo->deporte->nombre }}</p>
    </div>

    <!-- Formulario -->
    <form action="{{ route('torneos.equipos.store', $torneo) }}" method="POST" class="space-y-4 sm:space-y-6" id="formEquipo">
        @csrf

        <!-- Nombre del Equipo y Categoría -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Información del Equipo</h3>

            <div class="space-y-4">
                <!-- Categoría -->
                <div>
                    <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Categoría <span class="text-red-500">*</span>
                    </label>
                    @php
                        $categoriaPreseleccionada = old('categoria_id', request('categoria_id'));
                        $categoriaObj = $categoriaPreseleccionada ? $categorias->firstWhere('id', $categoriaPreseleccionada) : null;
                    @endphp

                    @if($categoriaObj)
                        {{-- Categoría pre-seleccionada: mostrar como badge + input hidden --}}
                        <input type="hidden" id="categoria_id" name="categoria_id" value="{{ $categoriaObj->id }}">
                        <div class="flex items-center gap-3 p-3 bg-indigo-50 border border-indigo-200 rounded-lg">
                            <span class="inline-flex items-center px-3 py-1 rounded-full bg-indigo-600 text-white text-sm font-semibold">
                                {{ $categoriaObj->nombre }}
                            </span>
                            <a href="{{ route('torneos.equipos.index', $torneo) }}" class="text-xs text-indigo-500 hover:text-indigo-700 underline">
                                Cambiar categoría
                            </a>
                        </div>
                    @else
                        {{-- Sin pre-selección: mostrar selector --}}
                        <select id="categoria_id" name="categoria_id" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('categoria_id') border-red-500 @enderror">
                            <option value="">Seleccionar categoría</option>
                            @foreach($categorias as $categoria)
                                <option value="{{ $categoria->id }}">
                                    {{ $categoria->nombre }}
                                </option>
                            @endforeach
                        </select>
                        <p class="mt-1 text-xs text-gray-500">Selecciona la categoría en la que competirá este equipo</p>
                    @endif
                    @error('categoria_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nombre del Equipo -->
                @if($torneo->deporte->requiereNombreEquipo())
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre del Equipo</label>
                    <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 @error('nombre') border-red-500 @enderror"
                        placeholder="Comienza a escribir para ver equipos anteriores..."
                        autocomplete="off">
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">
                        💡 Si escribes un nombre que ya usaste, se cargarán automáticamente los jugadores de ese equipo
                    </p>
                </div>
                @else
                <!-- Input oculto para deportes individuales/dobles (Padel, Tenis) -->
                <input type="hidden" id="nombre" name="nombre" value="{{ old('nombre') }}">
                @endif
            </div>
        </div>

        <!-- Selección de Jugadores -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-800">Jugadores del Equipo</h3>
                <button type="button" onclick="toggleModalCrearJugador()" class="mt-2 sm:mt-0 inline-flex items-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Jugador
                </button>
            </div>

            <!-- Selector de jugadores con Select2 -->
            <div>
                <label for="buscarJugador" class="block text-sm font-medium text-gray-700 mb-2">Buscar y Seleccionar Jugadores</label>
                <select id="buscarJugador" name="jugadores_temp[]" multiple class="w-full">
                    <optgroup label="Usuarios Registrados">
                        @foreach($usuarios as $usuario)
                            <option value="usuario_{{ $usuario->id }}"
                                    data-tipo="usuario"
                                    data-id="{{ $usuario->id }}"
                                    data-nombre="{{ $usuario->name }}"
                                    data-apellido="{{ $usuario->apellido }}"
                                    data-dni="{{ $usuario->dni ?? '' }}"
                                    data-jugador-id="{{ $usuario->jugador->id ?? '' }}"
                                    data-search="{{ strtolower($usuario->name . ' ' . $usuario->apellido . ' ' . ($usuario->dni ?? '')) }}">
                                {{ $usuario->name }} {{ $usuario->apellido }}{{ $usuario->dni ? ' - DNI: ' . $usuario->dni : '' }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="Jugadores">
                        @foreach($jugadores as $jugador)
                            <option value="jugador_{{ $jugador->id }}"
                                    data-tipo="jugador"
                                    data-id="{{ $jugador->id }}"
                                    data-nombre="{{ $jugador->nombre }}"
                                    data-apellido="{{ $jugador->apellido }}"
                                    data-dni="{{ $jugador->dni ?? '' }}"
                                    data-jugador-id="{{ $jugador->id }}"
                                    data-search="{{ strtolower(($jugador->nombre ?? '') . ' ' . ($jugador->apellido ?? '') . ' ' . ($jugador->dni ?? '')) }}">
                                {{ $jugador->nombre }} {{ $jugador->apellido }}{{ $jugador->dni ? ' - DNI: ' . $jugador->dni : '' }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
                <p class="mt-2 text-xs text-gray-500">
                    💡 Puedes buscar por nombre, apellido o DNI. Selecciona hasta {{ $maxJugadores }} {{ $maxJugadores === 1 ? 'jugador' : 'jugadores' }}.
                </p>
                <p id="avisoInscriptos" class="mt-1 text-xs text-amber-600 hidden">
                    ⚠️ Los jugadores ya inscriptos en un equipo de esta categoría aparecen deshabilitados.
                </p>
            </div>

            <!-- Inputs ocultos para enviar los jugadores seleccionados -->
            <div id="jugadoresHidden"></div>

            @error('jugadores')
                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
            @enderror

            <!-- Info -->
            <div class="mt-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                <p class="text-xs text-blue-800">
                    <strong>{{ $torneo->deporte->nombre }}:</strong>
                    Este deporte permite hasta {{ $maxJugadores }} {{ $maxJugadores === 1 ? 'jugador' : 'jugadores' }} por equipo.
                </p>
            </div>
        </div>

        <!-- Botones -->
        <div class="flex flex-wrap gap-3">
            <button type="submit" class="inline-flex items-center px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                Guardar Equipo
            </button>
            <a href="{{ route('torneos.equipos.index', $torneo) }}" class="inline-flex items-center px-6 py-2.5 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                Cancelar
            </a>
        </div>
    </form>
</div>

<!-- Modal Crear Jugador -->
<div id="modalCrearJugador" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Crear Nuevo Jugador</h3>
            <button type="button" onclick="toggleModalCrearJugador()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="formCrearJugador" onsubmit="crearJugador(event)" class="space-y-4">
            <div>
                <label for="jugador_nombre" class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                <input type="text" id="jugador_nombre" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="jugador_apellido" class="block text-sm font-medium text-gray-700 mb-1">Apellido</label>
                <input type="text" id="jugador_apellido" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="jugador_dni" class="block text-sm font-medium text-gray-700 mb-1">DNI (opcional)</label>
                <input type="text" id="jugador_dni" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="jugador_telefono" class="block text-sm font-medium text-gray-700 mb-1">Teléfono (opcional)</label>
                <input type="text" id="jugador_telefono" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="jugador_email" class="block text-sm font-medium text-gray-700 mb-1">Email (opcional)</label>
                <input type="email" id="jugador_email" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                <p class="mt-1 text-xs text-gray-500">Por aquí le llegarán las notificaciones del torneo</p>
            </div>
            <div>
                <label for="jugador_ranking" class="block text-sm font-medium text-gray-700 mb-1">Ranking (opcional)</label>
                <input type="text" id="jugador_ranking" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500" placeholder="Ej: 1234, 3.5, A+">
            </div>

            <div class="flex gap-3 pt-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg transition">
                    Crear y Agregar
                </button>
                <button type="button" onclick="toggleModalCrearJugador()" class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition">
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<!-- Select2 JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- jQuery UI (solo para autocomplete en Fútbol) -->
@if($torneo->deporte->requiereNombreEquipo())
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
@endif

<script>
const maxJugadores = {{ $maxJugadores }};
const requiereNombreEquipo = @json($torneo->deporte->requiereNombreEquipo());
const jugadoresInscritosPorCategoria = @json($jugadoresInscritosPorCategoria);

function actualizarJugadoresDisponibles(categoriaId) {
    const inscritosEnCategoria = jugadoresInscritosPorCategoria[categoriaId] || [];
    let hayInscritos = false;

    $('#buscarJugador option').each(function() {
        const $opt = $(this);
        const jugadorId = parseInt($opt.data('jugador-id'));
        const estaInscrito = jugadorId && inscritosEnCategoria.includes(jugadorId);

        if (estaInscrito) {
            const textoBase = $opt.data('texto-base') || $opt.text().replace(' ⚠️ Ya inscripto en esta categoría', '');
            $opt.data('texto-base', textoBase);
            $opt.text(textoBase + ' ⚠️ Ya inscripto en esta categoría');
            $opt.prop('disabled', true);
            hayInscritos = true;
        } else {
            const textoBase = $opt.data('texto-base');
            if (textoBase) $opt.text(textoBase);
            $opt.prop('disabled', false);
        }
    });

    // Remover seleccionados que ahora están inhabilitados
    const seleccionActual = $('#buscarJugador').val() || [];
    const seleccionFiltrada = seleccionActual.filter(val => {
        const $opt = $(`#buscarJugador option[value="${val}"]`);
        return !$opt.prop('disabled');
    });
    if (seleccionFiltrada.length !== seleccionActual.length) {
        $('#buscarJugador').val(seleccionFiltrada).trigger('change.select2');
        actualizarJugadoresHidden();
        autocompletarNombreEquipo();
    }

    document.getElementById('avisoInscriptos').classList.toggle('hidden', !hayInscritos);

    // Reinicializar Select2 para que tome los nuevos disabled
    reinicializarSelect2();
}

function reinicializarSelect2() {
    $('#buscarJugador').select2('destroy');
    inicializarSelect2();
}

function inicializarSelect2() {
    $('#buscarJugador').select2({
        placeholder: 'Busca por nombre, apellido o DNI...',
        allowClear: true,
        maximumSelectionLength: maxJugadores,
        width: '100%',
        language: {
            maximumSelected: function() {
                return `Solo puedes seleccionar ${maxJugadores} jugador${maxJugadores > 1 ? 'es' : ''}`;
            },
            noResults: function() { return "No se encontraron jugadores"; },
            searching: function() { return "Buscando..."; }
        }
    });

    $('#buscarJugador').on('change', function() {
        actualizarJugadoresHidden();
        autocompletarNombreEquipo();
    });
}

function toggleModalCrearJugador() {
    const modal = document.getElementById('modalCrearJugador');
    modal.classList.toggle('hidden');
}

function autocompletarNombreEquipo() {
    const inputNombre = document.getElementById('nombre');
    const selectedOptions = $('#buscarJugador').select2('data');

    // Para deportes que NO requieren nombre (Padel, Tenis), siempre generar automáticamente
    if (!requiereNombreEquipo) {
        if (selectedOptions.length > 0) {
            const apellidos = selectedOptions.map(option => {
                // Usar el campo apellido directamente del data attribute
                return $(option.element).data('apellido') || '';
            });
            inputNombre.value = apellidos.filter(a => a).join(' / ');
        } else {
            inputNombre.value = '';
        }
        return;
    }

    // Para deportes que SÍ requieren nombre (Futbol), solo autocompletar si no fue editado manualmente
    if (inputNombre.dataset.manuallyEdited === 'true') {
        return;
    }

    // Generar nombre con apellidos
    if (selectedOptions.length > 0) {
        const apellidos = selectedOptions.map(option => {
            // Usar el campo apellido directamente del data attribute
            return $(option.element).data('apellido') || '';
        });
        inputNombre.value = apellidos.filter(a => a).join(' / ');
    } else {
        inputNombre.value = '';
    }
}

// Actualizar inputs ocultos
function actualizarJugadoresHidden() {
    const container = document.getElementById('jugadoresHidden');
    const selectedValues = $('#buscarJugador').val() || [];

    container.innerHTML = selectedValues.map(value => {
        // value tiene formato "usuario_123" o "jugador_456"
        const [tipo, id] = value.split('_');
        return `<input type="hidden" name="jugadores[]" value="${id}">`;
    }).join('');

    console.log('Jugadores seleccionados:', selectedValues);
}

// Precargar jugadores desde plantilla de equipo
function precargarJugadoresDesdePlantilla(jugadoresIds) {
    if (!jugadoresIds || jugadoresIds.length === 0) {
        return;
    }

    // Limpiar selección actual
    $('#buscarJugador').val(null).trigger('change');

    // Construir valores con formato "jugador_ID"
    const valoresASeleccionar = jugadoresIds.map(id => `jugador_${id}`);

    // Seleccionar en Select2
    $('#buscarJugador').val(valoresASeleccionar).trigger('change');

    console.log('Jugadores precargados desde plantilla:', jugadoresIds);
}

$(document).ready(function() {
    // Inicializar Select2
    inicializarSelect2();

    // Aplicar filtro por categoría al cambiar selección
    $('#categoria_id').on('change', function() {
        const categoriaId = $(this).val();
        if (categoriaId) {
            actualizarJugadoresDisponibles(categoriaId);
        }
    });

    // Aplicar filtro si hay categoría preseleccionada (old input)
    const categoriaPreseleccionada = $('#categoria_id').val();
    if (categoriaPreseleccionada) {
        actualizarJugadoresDisponibles(categoriaPreseleccionada);
    }

    // Inicializar inputs ocultos al cargar
    actualizarJugadoresHidden();

    // Autocomplete para nombre de equipo (solo Fútbol)
    @if($torneo->deporte->requiereNombreEquipo())
    $('#nombre').autocomplete({
        minLength: 2,
        delay: 300,
        source: function(request, response) {
            $.ajax({
                url: '/api/equipos/autocomplete',
                method: 'GET',
                data: {
                    q: request.term,
                    deporte_id: {{ $torneo->deporte_id }}
                },
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(data) {
                    if (data.length === 0) {
                        response([]);
                        return;
                    }

                    response(data.map(function(equipo) {
                        return {
                            label: equipo.nombre,
                            value: equipo.nombre,
                            plantilla_id: equipo.id,
                            jugadores: equipo.jugadores,
                            veces_usado: equipo.veces_usado,
                            ultimo_uso: equipo.ultimo_uso
                        };
                    }));
                },
                error: function(xhr) {
                    console.error('Error en autocomplete:', xhr);
                    response([]);
                }
            });
        },
        select: function(event, ui) {
            // Marcar que el nombre fue seleccionado del autocomplete
            $('#nombre').data('plantilla-seleccionada', ui.item.plantilla_id);

            // Precargar jugadores del equipo
            if (ui.item.jugadores && ui.item.jugadores.length > 0) {
                precargarJugadoresDesdePlantilla(ui.item.jugadores);
            }

            return true;
        }
    }).autocomplete("instance")._renderItem = function(ul, item) {
        // Personalizar cómo se muestra cada item
        return $("<li>")
            .append(
                `<div class="equipo-sugerido">
                    <span class="equipo-nombre">${item.label}</span>
                    <span class="equipo-info">Usado ${item.veces_usado} ${item.veces_usado === 1 ? 'vez' : 'veces'}${item.ultimo_uso ? ' • ' + item.ultimo_uso : ''}</span>
                </div>`
            )
            .appendTo(ul);
    };
    @endif

    // Detectar edición manual del nombre solo para deportes que requieren nombre personalizado
    if (requiereNombreEquipo) {
        const inputNombre = document.getElementById('nombre');
        inputNombre.dataset.manuallyEdited = 'false';

        let timeoutId;
        inputNombre.addEventListener('input', function() {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                const value = inputNombre.value.trim();
                if (value && !value.includes('/')) {
                    inputNombre.dataset.manuallyEdited = 'true';
                }
            }, 500);
        });

        inputNombre.addEventListener('focus', function() {
            const value = inputNombre.value.trim();
            if (!value || value.includes('/')) {
                inputNombre.dataset.manuallyEdited = 'false';
            }
        });
    }
});

async function crearJugador(event) {
    event.preventDefault();

    const nombre = document.getElementById('jugador_nombre').value.trim();
    const apellido = document.getElementById('jugador_apellido').value.trim();
    const dni = document.getElementById('jugador_dni').value.trim();
    const telefono = document.getElementById('jugador_telefono').value.trim();
    const email = document.getElementById('jugador_email').value.trim();
    const ranking = document.getElementById('jugador_ranking').value.trim();

    // Validaciones básicas del frontend
    if (!nombre || !apellido) {
        alert('El nombre y apellido son obligatorios');
        return;
    }

    try {
        const response = await fetch('{{ route("jugadores.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                nombre: nombre,
                apellido: apellido,
                dni: dni || null,
                telefono: telefono || null,
                email: email || null,
                ranking: ranking || null
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Crear nueva opción para Select2
            const dniText = dni ? ` - DNI: ${dni}` : '';
            const newOption = new Option(
                `${nombre} ${apellido}${dniText}`,
                `jugador_${data.id}`,
                true,
                true
            );

            // Agregar data attributes
            $(newOption).data('tipo', 'jugador');
            $(newOption).data('id', data.id);
            $(newOption).data('nombre', nombre);
            $(newOption).data('apellido', apellido);
            $(newOption).data('dni', dni || '');
            $(newOption).data('inscrito', 'false');
            $(newOption).data('search', `${nombre} ${apellido} ${dni || ''}`.toLowerCase());

            // Agregar al optgroup de Jugadores
            $('#buscarJugador optgroup[label="Jugadores"]').append(newOption);

            // Reinicializar Select2 para que refresque su índice de búsqueda
            reinicializarSelect2();
            $('#buscarJugador').trigger('change');

            // Cerrar modal y limpiar form
            toggleModalCrearJugador();
            document.getElementById('formCrearJugador').reset();

            // Mostrar mensaje de éxito
            alert('✓ Jugador creado y agregado al equipo exitosamente');
        } else {
            // Manejar errores de validación
            let errorMsg = 'Error al crear jugador:\n\n';

            if (data.errors) {
                // Errores de validación Laravel
                Object.keys(data.errors).forEach(field => {
                    errorMsg += `• ${data.errors[field].join(', ')}\n`;
                });
            } else if (data.message) {
                errorMsg += data.message;
            } else {
                errorMsg += 'Error desconocido (código ' + response.status + ')';
            }

            alert(errorMsg);
            console.error('Error del servidor:', data);
        }
    } catch (error) {
        alert('Error de conexión al crear jugador.\n\nDetalles técnicos:\n' + error.message);
        console.error('Error completo:', error);
    }
}
</script>
@endpush
@endsection
