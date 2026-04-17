@extends('layouts.dashboard')

@section('title', 'Participantes - ' . $torneo->nombre)
@section('page-title', 'Gestión de Participantes')

@section('content')
<div class="max-w-6xl mx-auto space-y-4 sm:space-y-6">
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
                    <span class="text-gray-700 font-medium">Participantes</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Header con información de cupos -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $torneo->nombre }}</h2>
                <p class="text-sm text-gray-600 mt-1">{{ $torneo->deporte->nombre }}</p>
            </div>

            <!-- Indicador de cupos -->
            <div class="flex items-center gap-4">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-brand-600">{{ $cuposOcupados }}</div>
                    <div class="text-xs text-gray-500">Equipos</div>
                </div>
                <div class="text-gray-400">/</div>
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-gray-400">{{ $cuposTotales }}</div>
                    <div class="text-xs text-gray-500">Cupos</div>
                </div>
            </div>
        </div>

        <!-- Barra de progreso -->
        <div class="mt-4">
            <div class="flex items-center justify-between text-xs text-gray-600 mb-1">
                <span>Progreso de inscripción</span>
                <span>{{ $cuposOcupados }}/{{ $cuposTotales }} ({{ $cuposTotales > 0 ? round(($cuposOcupados / $cuposTotales) * 100) : 0 }}%)</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-2">
                @php
                    $porcentaje = $cuposTotales > 0 ? ($cuposOcupados / $cuposTotales) * 100 : 0;
                    $colorClass = $porcentaje >= 100 ? 'bg-green-600' : 'bg-brand-600';
                @endphp
                <div class="{{ $colorClass }} h-2 rounded-full transition-all duration-300" style="width: {{ min($porcentaje, 100) }}%"></div>
            </div>
        </div>

        <!-- Alertas de cupos -->
        @if($cuposDisponibles === 0)
            <div class="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                    ¡Torneo completo! Todos los cupos están ocupados. Puedes proceder a configurar los grupos.
                </p>
            </div>
        @elseif($cuposDisponibles <= 3 && $cuposDisponibles > 0)
            <div class="mt-4 p-3 bg-accent-50 border border-accent-200 rounded-lg">
                <p class="text-sm text-accent-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    ¡Quedan pocos cupos! Solo {{ $cuposDisponibles }} {{ $cuposDisponibles === 1 ? 'equipo' : 'equipos' }} más.
                </p>
            </div>
        @endif

        <!-- Aviso de datos dependientes -->
        @if($tieneGrupos || $tienePartidos)
            <div class="mt-4 bg-accent-50 border-l-4 border-accent-400 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-accent-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-accent-800">¡Atención! Hay configuraciones posteriores</h3>
                        <div class="mt-2 text-sm text-accent-700">
                            <p>El torneo ya tiene:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                @if($tieneGrupos)
                                    <li><strong>{{ $torneo->grupos()->count() }} grupos</strong> configurados</li>
                                @endif
                                @if($tienePartidos)
                                    <li><strong>{{ $torneo->partidos()->count() }} partidos</strong> programados</li>
                                @endif
                            </ul>
                            <p class="mt-2 font-semibold text-red-700">
                                No puedes eliminar equipos que ya están asignados a grupos/llaves. Si necesitas hacer cambios, primero resetea los grupos/llaves desde la sección correspondiente.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Botones de acción -->
    <div class="flex flex-wrap gap-2 sm:gap-3 items-center">
        @if($torneo->estado !== 'activo')
            <div class="inline-flex items-center px-4 py-2 bg-blue-50 text-blue-700 font-medium rounded-lg text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Solo lectura - El torneo ya fue publicado
            </div>
        @endif
        <a href="{{ route('torneos.show', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Torneo
        </a>
    </div>

    <!-- Tabs de Categoría + Botón Agregar por categoría -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Tabs scrollables (funciona en mobile y desktop) -->
        <div class="flex overflow-x-auto border-b border-gray-200" role="tablist">
            @foreach($torneo->categorias as $categoria)
                @php
                    $equiposCategoria = $equipos->where('categoria_id', $categoria->id);
                    if ($torneo->formato && $torneo->formato->tiene_grupos) {
                        $cuposCategoria = $categoria->pivot->numero_grupos * ($categoria->pivot->tamanio_grupo_id ? \App\Models\TamanioGrupo::find($categoria->pivot->tamanio_grupo_id)->tamanio : 0);
                    } else {
                        $cuposCategoria = $categoria->pivot->cupos_categoria ?? 0;
                    }
                    $catLlena = $cuposCategoria > 0 && $equiposCategoria->count() >= $cuposCategoria;
                @endphp
                <button type="button"
                        data-categoria="categoria-{{ $categoria->id }}"
                        data-categoria-id="{{ $categoria->id }}"
                        data-categoria-nombre="{{ $categoria->nombre }}"
                        data-cupos-llenos="{{ $catLlena ? '1' : '0' }}"
                        class="categoria-tab relative whitespace-nowrap flex-shrink-0 py-3.5 px-5 sm:px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm focus:outline-none transition-colors"
                        role="tab">
                    {{ $categoria->nombre }}
                    <span class="tab-count ml-1.5 text-xs font-normal text-gray-400">
                        {{ $equiposCategoria->count() }}/{{ $cuposCategoria }}
                        @if($catLlena)<span class="text-green-500 ml-0.5">✓</span>@endif
                    </span>
                </button>
            @endforeach
        </div>

        <!-- Barra de acción de la categoría activa -->
        @if($torneo->estado === 'activo')
        <div class="px-4 py-2.5 bg-gray-50 border-b border-gray-100 flex items-center justify-end">
            <a id="btn-agregar-categoria" href="#"
               class="inline-flex items-center px-3 py-1.5 sm:px-4 sm:py-2 bg-brand-600 hover:bg-brand-700 text-white text-sm font-semibold rounded-lg shadow-sm transition">
                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Agregar en <span id="btn-cat-nombre" class="ml-1 font-bold">...</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Listado de Equipos -->
    <!-- Mensaje vacío por categoría (controlado por JS) -->
    <div id="empty-categoria-msg" class="hidden bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
        <svg class="mx-auto h-14 w-14 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
        </svg>
        <p class="text-gray-500 text-sm">No hay equipos en esta categoría aún.</p>
    </div>

    @if($equipos->isEmpty())
        <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No hay equipos registrados</h3>
            <p class="text-sm text-gray-500 mb-6">Comienza agregando el primer equipo al torneo.</p>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($equipos as $equipo)
                <div class="equipo-card bg-white rounded-lg shadow-sm p-4 hover:shadow-md transition" data-categoria="categoria-{{ $equipo->categoria_id }}">
                    <div class="flex items-start justify-between mb-3">
                        <div class="flex-1">
                            <div class="flex items-center gap-2 mb-1">
                                <h3 class="font-semibold text-gray-800 text-base">{{ $equipo->nombre }}</h3>
                                @if($equipo->categoria)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-brand-100 text-brand-800">
                                        {{ $equipo->categoria->nombre }}
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-gray-500 mt-1">
                                {{ $equipo->jugadores->count() }} {{ $equipo->jugadores->count() === 1 ? 'jugador' : 'jugadores' }}
                            </p>
                        </div>
                        <div class="flex gap-2">
                            <!-- Botón de Imprimir Planilla (siempre visible) -->
                            <a href="{{ route('torneos.equipos.planilla', [$torneo, $equipo]) }}"
                               class="text-brand-600 hover:text-brand-800 p-1"
                               title="Descargar planilla en PDF"
                               target="_blank">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                            </a>

                            <!-- Botón de Eliminar (solo en activo) -->
                            @if($torneo->estado === 'activo')
                                <form action="{{ route('torneos.equipos.destroy', [$torneo, $equipo]) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este equipo?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Eliminar equipo">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-3">
                        <h4 class="text-xs font-semibold text-gray-500 uppercase mb-2">Jugadores</h4>
                        <ul class="space-y-1">
                            @foreach($equipo->jugadores->sortBy('pivot.orden') as $jugador)
                                <li class="text-sm text-gray-700 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                                    </svg>
                                    {{ $jugador->nombre_completo }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabs = document.querySelectorAll('.categoria-tab');
    const equipoCards = document.querySelectorAll('.equipo-card');
    const btnAgregar = document.getElementById('btn-agregar-categoria');
    const btnCatNombre = document.getElementById('btn-cat-nombre');
    const emptyCatMsg = document.getElementById('empty-categoria-msg');
    const baseCrearUrl = "{{ route('torneos.equipos.create', $torneo) }}";

    function activarTab(tab) {
        const categoriaSelector = tab.dataset.categoria;
        const categoriaId = tab.dataset.categoriaId;
        const categoriaNombre = tab.dataset.categoriaNombre;
        const cuposLlenos = tab.dataset.cuposLlenos === '1';

        // Actualizar estilos de tabs
        tabs.forEach(t => {
            t.classList.remove('border-brand-600', 'text-brand-600');
            t.classList.add('border-transparent', 'text-gray-500');
            const count = t.querySelector('.tab-count');
            if (count) count.classList.remove('text-brand-400');
        });
        tab.classList.remove('border-transparent', 'text-gray-500');
        tab.classList.add('border-brand-600', 'text-brand-600');
        const activeCount = tab.querySelector('.tab-count');
        if (activeCount) activeCount.classList.add('text-brand-400');

        // Filtrar cards
        let hayVisibles = false;
        equipoCards.forEach(card => {
            const match = card.dataset.categoria === categoriaSelector;
            card.classList.toggle('hidden', !match);
            if (match) hayVisibles = true;
        });

        // Mostrar/ocultar mensaje vacío
        if (emptyCatMsg) emptyCatMsg.classList.toggle('hidden', hayVisibles);

        // Actualizar botón agregar
        if (btnAgregar) {
            btnAgregar.href = `${baseCrearUrl}?categoria_id=${categoriaId}`;
            btnAgregar.classList.toggle('hidden', cuposLlenos);
        }
        if (btnCatNombre) btnCatNombre.textContent = categoriaNombre;
    }

    tabs.forEach(tab => tab.addEventListener('click', () => activarTab(tab)));

    // Activar primera categoría por defecto
    if (tabs.length > 0) activarTab(tabs[0]);
});
</script>
@endsection
