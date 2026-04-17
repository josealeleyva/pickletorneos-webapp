@extends('layouts.dashboard')

@section('title', 'Configuración de Grupos - ' . $torneo->nombre)
@section('page-title', 'Configuración de Grupos')

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
                    <span class="text-gray-700 font-medium">Grupos</span>
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
                    {{ $torneo->categorias->count() }} {{ $torneo->categorias->count() === 1 ? 'categoría' : 'categorías' }} •
                    {{ $grupos->count() }} grupos totales
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-indigo-600">{{ $equiposTotales }}</div>
                    <div class="text-xs text-gray-500">Equipos</div>
                </div>
                <div class="text-gray-400">/</div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-gray-400">{{ $cuposTotales }}</div>
                    <div class="text-xs text-gray-500">Requeridos</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex flex-wrap gap-2 sm:gap-3">
        @if($equiposTotales === $cuposTotales)
            @if(!$gruposConfigurados)
                <form action="{{ route('torneos.grupos.sortear', $torneo) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sortear Equipos
                    </button>
                </form>
            @else
                <form action="{{ route('torneos.grupos.resetear', $torneo) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de resetear los grupos? Esto eliminará la configuración actual.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Resetear y Sortear Nuevamente
                    </button>
                </form>
            @endif
        @else
            <div class="inline-flex items-center px-4 py-2 bg-gray-400 text-white font-semibold rounded-lg shadow-md cursor-not-allowed text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                Faltan {{ $cuposTotales - $equiposTotales }} Equipos
            </div>
        @endif

        <a href="{{ route('torneos.equipos.index', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            Gestionar Equipos
        </a>

        <a href="{{ route('torneos.show', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Torneo
        </a>
    </div>

    <!-- Alerta de cupos incompletos -->
    @if($equiposTotales < $cuposTotales)
        <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-amber-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-amber-900">Equipos Incompletos</h3>
                    <p class="text-sm text-amber-800 mt-1">
                        El torneo requiere {{ $cuposTotales }} equipos en total.
                        Actualmente hay {{ $equiposTotales }} equipo{{ $equiposTotales === 1 ? '' : 's' }}.
                        <strong>Faltan {{ $cuposTotales - $equiposTotales }} equipo{{ ($cuposTotales - $equiposTotales) === 1 ? '' : 's' }}.</strong>
                    </p>
                    <div class="mt-2 text-xs text-amber-700 space-y-1">
                        @foreach($torneo->categorias as $categoria)
                            @php
                                $numeroGrupos = $categoria->pivot->numero_grupos;
                                $tamanioGrupoId = $categoria->pivot->tamanio_grupo_id;
                                $cuposCategoria = 0;
                                if ($numeroGrupos && $tamanioGrupoId) {
                                    $tamanioGrupo = \App\Models\TamanioGrupo::find($tamanioGrupoId);
                                    if ($tamanioGrupo) {
                                        $cuposCategoria = $numeroGrupos * $tamanioGrupo->tamanio;
                                    }
                                }
                                $equiposCategoria = $torneo->equipos()->where('categoria_id', $categoria->id)->count();
                            @endphp
                            <div>
                                • {{ $categoria->nombre }}: {{ $equiposCategoria }}/{{ $cuposCategoria }} equipos
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Aviso de fixture generado -->
    @if($tienePartidos && $gruposConfigurados)
        <div class="bg-amber-50 border-l-4 border-amber-400 p-4">
            <div class="flex">
                <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-amber-800">¡Atención! Ya hay un fixture generado</h3>
                    <div class="mt-2 text-sm text-amber-700">
                        <p>El torneo ya tiene <strong>{{ $torneo->partidos()->count() }} partidos</strong> generados en el fixture.</p>
                        <p class="mt-2 font-semibold text-red-700">
                            Si reseteas los grupos, se eliminarán todos los partidos y la configuración del fixture. Esta acción no se puede deshacer.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Grupos -->
    @if($gruposConfigurados)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-blue-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1 a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <strong>Tip:</strong> Click en el botón de intercambio (⇄) para reorganizar equipos entre grupos de la misma categoría
            </p>
        </div>

        <!-- Tabs por categoría -->
        <!-- Desktop: Tabs horizontales -->
        <div class="hidden sm:block border-b border-gray-200 mb-4">
            <nav class="flex overflow-x-auto" aria-label="Tabs">
                <button type="button" data-categoria="all" class="categoria-tab whitespace-nowrap py-4 px-6 border-b-2 border-indigo-600 font-medium text-sm text-indigo-600">
                    Todas ({{ $grupos->count() }})
                </button>
                @foreach($torneo->categorias as $categoria)
                    @php
                        $gruposCategoria = $grupos->where('categoria_id', $categoria->id);
                    @endphp
                    <button type="button" data-categoria="categoria-{{ $categoria->id }}" class="categoria-tab whitespace-nowrap py-4 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                        {{ $categoria->nombre }} ({{ $gruposCategoria->count() }})
                    </button>
                @endforeach
            </nav>
        </div>

        <!-- Mobile: Selector dropdown -->
        <div class="sm:hidden mb-4">
            <select id="categoria-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                <option value="all">Todas las Categorías ({{ $grupos->count() }})</option>
                @foreach($torneo->categorias as $categoria)
                    @php
                        $gruposCategoria = $grupos->where('categoria_id', $categoria->id);
                    @endphp
                    <option value="categoria-{{ $categoria->id }}">{{ $categoria->nombre }} ({{ $gruposCategoria->count() }})</option>
                @endforeach
            </select>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($grupos as $grupo)
                @php
                    $tamanioGrupoCategoria = null;
                    if ($grupo->categoria_id) {
                        $categoriaGrupo = $torneo->categorias->firstWhere('id', $grupo->categoria_id);
                        if ($categoriaGrupo && $categoriaGrupo->pivot->tamanio_grupo_id) {
                            $tamanioGrupoCategoria = \App\Models\TamanioGrupo::find($categoriaGrupo->pivot->tamanio_grupo_id);
                        }
                    }
                @endphp
                <div class="grupo-card bg-white rounded-lg shadow-sm overflow-hidden" data-categoria="categoria-{{ $grupo->categoria_id }}">
                    <div class="bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-4 py-3">
                        <h3 class="font-bold text-lg">{{ $grupo->nombre }}</h3>
                        <p class="text-xs text-indigo-100">
                            {{ $grupo->equipos->count() }}/{{ $tamanioGrupoCategoria ? $tamanioGrupoCategoria->tamanio : '?' }} equipos
                        </p>
                    </div>

                    <div class="p-4">
                        @if($grupo->equipos->isEmpty())
                            <div class="text-center py-6 text-gray-400 text-sm">
                                <svg class="mx-auto h-10 w-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                Sin equipos
                            </div>
                        @else
                            <ul class="space-y-2">
                                @foreach($grupo->equipos->sortBy('nombre') as $equipo)
                                    <li class="p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                        <div class="flex items-start gap-2">
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-800 text-sm">{{ $equipo->nombre }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @foreach($equipo->jugadores->sortBy('pivot.orden') as $jugador)
                                                        <span>{{ $jugador->nombre_completo }}</span>{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </div>
                                            </div>
                                            <button
                                                data-equipo-id="{{ $equipo->id }}"
                                                data-equipo-nombre="{{ $equipo->nombre }}"
                                                data-grupo-nombre="{{ $grupo->nombre }}"
                                                data-categoria-id="{{ $equipo->categoria_id }}"
                                                onclick="abrirModalIntercambio(this)"
                                                class="flex-shrink-0 p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded transition"
                                                title="Intercambiar con otro equipo">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Resumen de avance por categoría -->
        <div class="space-y-3">
            @foreach($torneo->categorias as $categoria)
                @php
                    $avanceGrupoId = $categoria->pivot->avance_grupos_id;
                    $numeroGrupos = $categoria->pivot->numero_grupos;
                    if ($avanceGrupoId) {
                        $avanceGrupo = \App\Models\AvanceGrupo::find($avanceGrupoId);
                    } else {
                        $avanceGrupo = null;
                    }
                @endphp
                @if($avanceGrupo)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Clasificación {{ $categoria->nombre }}
                        </h3>
                        <p class="text-sm text-blue-800">
                            <strong>{{ $avanceGrupo->nombre }}:</strong>
                            @php
                                $directos = $avanceGrupo->cantidad_avanza_directo;
                                $mejores = $avanceGrupo->cantidad_avanza_mejores;
                                $totalAvanzan = ($directos * $numeroGrupos) + $mejores;
                            @endphp
                            Clasifican {{ $totalAvanzan }} equipos
                            @if($directos > 0 && $mejores > 0)
                                ({{ $directos }} de cada grupo + {{ $mejores }} mejores {{ $mejores === 1 ? 'segundo' : 'segundos' }})
                            @elseif($directos > 0)
                                ({{ $directos }} de cada grupo)
                            @endif
                        </p>
                    </div>
                @endif
            @endforeach
        </div>
    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Grupos no configurados</h3>
            <p class="text-sm text-gray-500 mb-6">
                @if($equiposTotales === $cuposTotales)
                    Los {{ $equiposTotales }} equipos están listos. Realiza el sorteo para distribuirlos en los grupos de cada categoría.
                @else
                    Primero debes completar los {{ $cuposTotales }} equipos requeridos para el torneo.
                @endif
            </p>
        </div>
    @endif
</div>

<!-- Modal de Intercambio -->
<div id="modalIntercambio" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-2xl w-full max-h-[80vh] overflow-y-auto">
        <div class="sticky top-0 bg-white border-b border-gray-200 px-6 py-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-semibold text-gray-800">Intercambiar Equipo</h3>
                <button onclick="cerrarModalIntercambio()" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p class="text-sm text-gray-600 mt-2">
                Selecciona con qué equipo quieres intercambiar <strong id="equipo-origen-nombre"></strong>
            </p>
        </div>

        <div class="p-6" id="listaEquiposIntercambio">
            <!-- Se llena dinámicamente con JavaScript -->
        </div>
    </div>
</div>

@if($gruposConfigurados)
@push('scripts')
<script>
let equipoOrigenId = null;
let equipoOrigenNombre = '';
let grupoOrigenNombre = '';
let categoriaOrigenId = null;

// Datos de todos los equipos por grupo (desde el servidor)
const equiposPorGrupo = @json($equiposPorGrupo);

// JavaScript para filtrado por categoría
const grupoCards = document.querySelectorAll('.grupo-card');
const tabs = document.querySelectorAll('.categoria-tab');
const categoriaSelect = document.getElementById('categoria-select');

function filtrarPorCategoria(categoriaId) {
    grupoCards.forEach(card => {
        if (categoriaId === 'all' || card.dataset.categoria === categoriaId) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });

    // Actualizar tabs (desktop)
    tabs.forEach(tab => {
        if (tab.dataset.categoria === categoriaId) {
            tab.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            tab.classList.add('border-indigo-600', 'text-indigo-600');
        } else {
            tab.classList.remove('border-indigo-600', 'text-indigo-600');
            tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
        }
    });
}

// Event listeners para tabs desktop
tabs.forEach(tab => {
    tab.addEventListener('click', function() {
        filtrarPorCategoria(this.dataset.categoria);
    });
});

// Event listener para selector mobile
if (categoriaSelect) {
    categoriaSelect.addEventListener('change', function() {
        filtrarPorCategoria(this.value);
    });
}

function abrirModalIntercambio(button) {
    equipoOrigenId = parseInt(button.dataset.equipoId);
    equipoOrigenNombre = button.dataset.equipoNombre;
    grupoOrigenNombre = button.dataset.grupoNombre;
    categoriaOrigenId = parseInt(button.dataset.categoriaId);

    document.getElementById('equipo-origen-nombre').textContent = equipoOrigenNombre + ' (' + grupoOrigenNombre + ')';

    // Generar lista de equipos para intercambiar
    let html = '';

    for (const [nombreGrupo, equipos] of Object.entries(equiposPorGrupo)) {
        // Saltar el grupo actual
        if (nombreGrupo === grupoOrigenNombre) {
            continue;
        }

        // Filtrar equipos de la misma categoría
        const equiposMismaCategoria = equipos.filter(eq => eq.categoria_id === categoriaOrigenId && eq.id !== equipoOrigenId);

        if (equiposMismaCategoria.length > 0) {
            html += `
                <div class="mb-6">
                    <h4 class="text-sm font-semibold text-gray-700 mb-3">${nombreGrupo}</h4>
                    <div class="space-y-2">
            `;

            equiposMismaCategoria.forEach(equipo => {
                html += `
                    <button
                        onclick="intercambiarEquipos(${equipo.id}, '${equipo.nombre.replace(/'/g, "\\'")}')"
                        class="w-full text-left p-3 bg-gray-50 hover:bg-indigo-50 border border-gray-200 hover:border-indigo-300 rounded-lg transition">
                        <div class="font-medium text-gray-800 text-sm">${equipo.nombre}</div>
                        <div class="text-xs text-gray-500 mt-1">${equipo.jugadores}</div>
                    </button>
                `;
            });

            html += `
                    </div>
                </div>
            `;
        }
    }

    if (!html) {
        html = '<p class="text-gray-500 text-center py-8">No hay equipos disponibles para intercambiar en esta categoría.</p>';
    }

    document.getElementById('listaEquiposIntercambio').innerHTML = html;
    document.getElementById('modalIntercambio').classList.remove('hidden');
}

function cerrarModalIntercambio() {
    document.getElementById('modalIntercambio').classList.add('hidden');
    equipoOrigenId = null;
}

async function intercambiarEquipos(equipoDestinoId, equipoDestinoNombre) {
    if (!equipoOrigenId) {
        return;
    }

    try {
        const response = await fetch('{{ route("torneos.grupos.intercambiar", $torneo) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                equipo1_id: equipoOrigenId,
                equipo2_id: equipoDestinoId
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Recargar la página para mostrar los cambios
            window.location.reload();
        } else {
            alert(data.error || 'Error al intercambiar equipos');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al intercambiar equipos. Por favor intenta nuevamente.');
    }
}

// Cerrar modal al hacer click fuera
document.getElementById('modalIntercambio').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModalIntercambio();
    }
});
</script>
@endpush
@endif
@endsection
