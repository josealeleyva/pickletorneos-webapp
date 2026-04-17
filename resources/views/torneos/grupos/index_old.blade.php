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
                    {{ $torneo->numero_grupos }} grupos de {{ $torneo->tamanioGrupo->tamanio }} equipos
                </p>
            </div>

            <div class="flex items-center gap-3">
                <div class="text-center">
                    <div class="text-2xl sm:text-3xl font-bold text-brand-600">{{ $equiposTotales }}</div>
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
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
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

        <a href="{{ route('torneos.equipos.index', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
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
        <div class="bg-accent-50 border border-accent-200 rounded-lg p-4">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-accent-600 mr-3 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <h3 class="text-sm font-semibold text-accent-900">Equipos Incompletos</h3>
                    <p class="text-sm text-accent-800 mt-1">
                        El torneo requiere {{ $cuposTotales }} equipos ({{ $torneo->numero_grupos }} grupos × {{ $torneo->tamanioGrupo->tamanio }} equipos).
                        Actualmente hay {{ $equiposTotales }} equipo{{ $equiposTotales === 1 ? '' : 's' }}.
                        <strong>Faltan {{ $cuposTotales - $equiposTotales }} equipo{{ ($cuposTotales - $equiposTotales) === 1 ? '' : 's' }}.</strong>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Grupos -->
    @if($gruposConfigurados)
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
            <p class="text-sm text-blue-800 flex items-center">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <strong>Tip:</strong> Arrastra y suelta equipos para reorganizar los grupos
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-{{ min($torneo->numero_grupos, 4) }} gap-4">
            @foreach($grupos as $grupo)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden grupo-container" data-grupo-id="{{ $grupo->id }}" data-max-equipos="{{ $torneo->tamanioGrupo->tamanio }}">
                    <div class="bg-gradient-to-r from-brand-600 to-purple-600 text-white px-4 py-3">
                        <h3 class="font-bold text-lg">{{ $grupo->nombre }}</h3>
                        <p class="text-xs text-brand-100 contador-equipos">{{ $grupo->equipos->count() }}/{{ $torneo->tamanioGrupo->tamanio }} equipos</p>
                    </div>

                    <div class="p-4 grupo-dropzone min-h-[200px]" data-grupo-id="{{ $grupo->id }}">
                        @if($grupo->equipos->isEmpty())
                            <div class="text-center py-6 text-gray-400 text-sm empty-state">
                                <svg class="mx-auto h-10 w-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                Arrastra equipos aquí
                            </div>
                        @else
                            <ul class="space-y-2 equipos-list">
                                @foreach($grupo->equipos->sortBy('nombre') as $equipo)
                                    <li class="equipo-item p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition cursor-move"
                                        draggable="true"
                                        data-equipo-id="{{ $equipo->id }}"
                                        data-equipo-nombre="{{ $equipo->nombre }}">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"></path>
                                            </svg>
                                            <div class="flex-1">
                                                <div class="font-medium text-gray-800 text-sm">{{ $equipo->nombre }}</div>
                                                <div class="text-xs text-gray-500 mt-1">
                                                    @foreach($equipo->jugadores->sortBy('pivot.orden') as $jugador)
                                                        <span>{{ $jugador->nombre_completo }}</span>{{ !$loop->last ? ', ' : '' }}
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Resumen de avance -->
        @if($torneo->avanceGrupo)
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                    </svg>
                    Clasificación a Fase de Eliminación
                </h3>
                <p class="text-sm text-blue-800">
                    <strong>{{ $torneo->avanceGrupo->nombre }}:</strong>
                    @php
                        $directos = $torneo->avanceGrupo->cantidad_avanza_directo;
                        $mejores = $torneo->avanceGrupo->cantidad_avanza_mejores;
                        $totalAvanzan = ($directos * $torneo->numero_grupos) + $mejores;
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
    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
            <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">Grupos no configurados</h3>
            <p class="text-sm text-gray-500 mb-6">
                @if($equiposTotales === $cuposTotales)
                    Los {{ $equiposTotales }} equipos están listos. Realiza el sorteo para distribuirlos en {{ $torneo->numero_grupos }} grupos.
                @else
                    Primero debes completar los {{ $cuposTotales }} equipos requeridos para el torneo.
                @endif
            </p>
        </div>
    @endif
</div>

@if($gruposConfigurados)
@push('scripts')
<script>
let draggedElement = null;
let sourceGrupoId = null;

// Inicializar drag & drop
document.addEventListener('DOMContentLoaded', function() {
    initializeDragAndDrop();
});

function initializeDragAndDrop() {
    // Elementos arrastrables
    const equipoItems = document.querySelectorAll('.equipo-item');
    equipoItems.forEach(item => {
        item.addEventListener('dragstart', handleDragStart);
        item.addEventListener('dragend', handleDragEnd);
    });

    // Zonas de drop
    const dropzones = document.querySelectorAll('.grupo-dropzone');
    dropzones.forEach(zone => {
        zone.addEventListener('dragover', handleDragOver);
        zone.addEventListener('drop', handleDrop);
        zone.addEventListener('dragenter', handleDragEnter);
        zone.addEventListener('dragleave', handleDragLeave);
    });
}

function handleDragStart(e) {
    draggedElement = e.currentTarget;
    sourceGrupoId = draggedElement.closest('.grupo-dropzone').dataset.grupoId;

    e.currentTarget.style.opacity = '0.4';
    e.dataTransfer.effectAllowed = 'move';
    e.dataTransfer.setData('text/html', e.currentTarget.innerHTML);
}

function handleDragEnd(e) {
    e.currentTarget.style.opacity = '1';

    // Limpiar estilos de todos los dropzones
    document.querySelectorAll('.grupo-dropzone').forEach(zone => {
        zone.classList.remove('bg-green-50', 'border-2', 'border-green-300', 'border-dashed');
        zone.classList.remove('bg-red-50', 'border-2', 'border-red-300', 'border-dashed');
    });
}

function handleDragOver(e) {
    if (e.preventDefault) {
        e.preventDefault();
    }
    e.dataTransfer.dropEffect = 'move';
    return false;
}

function handleDragEnter(e) {
    const dropzone = e.currentTarget;
    const targetGrupoId = dropzone.dataset.grupoId;

    // No hacer nada si es el mismo grupo
    if (targetGrupoId === sourceGrupoId) {
        return;
    }

    // Verificar capacidad del grupo
    const grupoContainer = dropzone.closest('.grupo-container');
    const maxEquipos = parseInt(grupoContainer.dataset.maxEquipos);
    const equiposActuales = dropzone.querySelectorAll('.equipo-item').length;

    if (equiposActuales >= maxEquipos) {
        // Grupo lleno - rojo
        dropzone.classList.add('bg-red-50', 'border-2', 'border-red-300', 'border-dashed');
    } else {
        // Hay espacio - verde
        dropzone.classList.add('bg-green-50', 'border-2', 'border-green-300', 'border-dashed');
    }
}

function handleDragLeave(e) {
    const dropzone = e.currentTarget;

    // Solo remover si realmente salimos del dropzone (no de un hijo)
    if (e.target === dropzone) {
        dropzone.classList.remove('bg-green-50', 'border-2', 'border-green-300', 'border-dashed');
        dropzone.classList.remove('bg-red-50', 'border-2', 'border-red-300', 'border-dashed');
    }
}

async function handleDrop(e) {
    if (e.stopPropagation) {
        e.stopPropagation();
    }
    e.preventDefault();

    const dropzone = e.currentTarget;
    const targetGrupoId = dropzone.dataset.grupoId;

    // Limpiar estilos
    dropzone.classList.remove('bg-green-50', 'border-2', 'border-green-300', 'border-dashed');
    dropzone.classList.remove('bg-red-50', 'border-2', 'border-red-300', 'border-dashed');

    // No hacer nada si es el mismo grupo
    if (targetGrupoId === sourceGrupoId) {
        return false;
    }

    // Verificar capacidad
    const grupoContainer = dropzone.closest('.grupo-container');
    const maxEquipos = parseInt(grupoContainer.dataset.maxEquipos);
    const equiposActuales = dropzone.querySelectorAll('.equipo-item').length;

    if (equiposActuales >= maxEquipos) {
        alert('El grupo ya está completo. No se pueden agregar más equipos.');
        return false;
    }

    // Obtener datos del equipo
    const equipoId = draggedElement.dataset.equipoId;
    const equipoNombre = draggedElement.dataset.equipoNombre;

    // Hacer la petición al servidor
    try {
        const response = await fetch('{{ route("torneos.grupos.asignar", $torneo) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                equipo_id: equipoId,
                grupo_id: targetGrupoId
            })
        });

        const data = await response.json();

        if (response.ok) {
            // Mover visualmente el elemento
            moverEquipoVisualmente(draggedElement, dropzone, targetGrupoId);

            // Mostrar mensaje de éxito (opcional, comentado para no ser intrusivo)
            // alert(data.message);
        } else {
            alert(data.error || 'Error al mover el equipo');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al mover el equipo. Por favor intenta nuevamente.');
    }

    return false;
}

function moverEquipoVisualmente(elemento, nuevoDropzone, nuevoGrupoId) {
    const dropzoneAnterior = elemento.closest('.grupo-dropzone');

    // Obtener o crear la lista de equipos en el nuevo dropzone
    let nuevaLista = nuevoDropzone.querySelector('.equipos-list');

    if (!nuevaLista) {
        // Crear lista si no existe (grupo vacío)
        const emptyState = nuevoDropzone.querySelector('.empty-state');
        if (emptyState) {
            emptyState.remove();
        }

        nuevaLista = document.createElement('ul');
        nuevaLista.className = 'space-y-2 equipos-list';
        nuevoDropzone.appendChild(nuevaLista);
    }

    // Mover el elemento
    nuevaLista.appendChild(elemento);

    // Actualizar contadores
    actualizarContador(dropzoneAnterior);
    actualizarContador(nuevoDropzone);

    // Verificar si el grupo anterior quedó vacío
    const listaAnterior = dropzoneAnterior.querySelector('.equipos-list');
    if (listaAnterior && listaAnterior.children.length === 0) {
        listaAnterior.remove();

        // Agregar empty state
        const emptyState = document.createElement('div');
        emptyState.className = 'text-center py-6 text-gray-400 text-sm empty-state';
        emptyState.innerHTML = `
            <svg class="mx-auto h-10 w-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
            </svg>
            Arrastra equipos aquí
        `;
        dropzoneAnterior.appendChild(emptyState);
    }

    // Reinicializar eventos para el elemento movido
    elemento.addEventListener('dragstart', handleDragStart);
    elemento.addEventListener('dragend', handleDragEnd);
}

function actualizarContador(dropzone) {
    const grupoContainer = dropzone.closest('.grupo-container');
    const contador = grupoContainer.querySelector('.contador-equipos');
    const maxEquipos = parseInt(grupoContainer.dataset.maxEquipos);
    const equiposActuales = dropzone.querySelectorAll('.equipo-item').length;

    contador.textContent = `${equiposActuales}/${maxEquipos} equipos`;
}
</script>
@endpush
@endif
@endsection
