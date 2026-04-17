@extends('layouts.dashboard')

@section('title', 'Vista Previa - Llaves - ' . $torneo->nombre)
@section('page-title', 'Vista Previa - Llaves de Eliminación')

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
                    <span class="text-gray-700 font-medium">Vista Previa Llaves</span>
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
                    Vista Previa - Llaves de Eliminación
                </p>
            </div>
        </div>
    </div>

    <!-- Botones de acción -->
    <div class="flex flex-wrap gap-2 sm:gap-3">
        <button type="submit" form="generateForm"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition text-sm"
                onclick="return prepareFormSubmit()">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Generar Llaves
        </button>

        <a href="{{ route('torneos.show', $torneo) }}"
           class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Torneo
        </a>
    </div>

    <form method="POST" action="{{ route('torneos.llaves.generate', $torneo) }}" id="generateForm">
        @csrf

        <!-- Checkbox para 3er puesto -->
        <div class="bg-white rounded-lg shadow-sm p-4 mb-6">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="tercer_puesto" value="1" class="rounded border-gray-300 text-brand-600 focus:ring-blue-500">
                <span class="text-gray-700 font-medium">Incluir partido por el 3er y 4to puesto</span>
            </label>
        </div>

        <!-- Tabs por categoría (Desktop) -->
        <div class="hidden md:block">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    @foreach($clasificadosPorCategoria as $catId => $data)
                        <button type="button"
                                class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm {{ $loop->first ? 'border-blue-500 text-brand-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}"
                                data-tab="categoria-{{ $catId }}">
                            {{ $data['categoria']->nombre }}
                            <span class="ml-2 text-xs bg-blue-100 text-brand-600 px-2 py-1 rounded-full">
                                {{ $data['total'] }} equipos
                            </span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <div class="mt-6">
                @foreach($clasificadosPorCategoria as $catId => $data)
                    <div class="tab-content {{ $loop->first ? '' : 'hidden' }}" id="categoria-{{ $catId }}">
                        @include('torneos.llaves.partials.preview-categoria', [
                            'categoria' => $data['categoria'],
                            'clasificados' => $data['clasificados'],
                            'total' => $data['total'],
                            'torneo' => $torneo
                        ])
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Accordion por categoría (Mobile) -->
        <div class="md:hidden space-y-4">
            @foreach($clasificadosPorCategoria as $catId => $data)
                <div class="border border-gray-200 rounded-lg overflow-hidden">
                    <button type="button"
                            class="accordion-button w-full px-4 py-3 bg-gray-50 text-left flex items-center justify-between"
                            data-accordion="accordion-{{ $catId }}">
                        <div>
                            <span class="font-medium text-gray-800">{{ $data['categoria']->nombre }}</span>
                            <span class="ml-2 text-xs bg-blue-100 text-brand-600 px-2 py-1 rounded-full">
                                {{ $data['total'] }} equipos
                            </span>
                        </div>
                        <svg class="w-5 h-5 text-gray-500 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div class="accordion-content {{ $loop->first ? '' : 'hidden' }} p-4" id="accordion-{{ $catId }}">
                        @include('torneos.llaves.partials.preview-categoria', [
                            'categoria' => $data['categoria'],
                            'clasificados' => $data['clasificados'],
                            'total' => $data['total'],
                            'torneo' => $torneo
                        ])
                    </div>
                </div>
            @endforeach
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Limpiar duplicados antes de enviar el formulario
    function prepareFormSubmit() {
        // Obtener todos los inputs de equipos por categoría
        const form = document.getElementById('generateForm');
        const categorias = {};

        // Agrupar inputs por categoría
        form.querySelectorAll('.team-input').forEach(input => {
            const match = input.name.match(/categorias\[(\d+)\]\[clasificados\]\[\]/);
            if (match) {
                const catId = match[1];
                if (!categorias[catId]) {
                    categorias[catId] = [];
                }
                if (input.value && !categorias[catId].includes(input.value)) {
                    categorias[catId].push(input.value);
                }
            }
        });

        // Remover todos los inputs existentes de clasificados
        form.querySelectorAll('.team-input').forEach(input => input.remove());

        // Crear nuevos inputs sin duplicados
        Object.keys(categorias).forEach(catId => {
            categorias[catId].forEach(equipoId => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = `categorias[${catId}][clasificados][]`;
                input.value = equipoId;
                form.appendChild(input);
            });
        });

        return true;
    }
    // Tabs desktop
    document.querySelectorAll('.tab-button').forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.tab;

            // Actualizar botones
            document.querySelectorAll('.tab-button').forEach(btn => {
                btn.classList.remove('border-blue-500', 'text-brand-600');
                btn.classList.add('border-transparent', 'text-gray-500');
            });
            this.classList.remove('border-transparent', 'text-gray-500');
            this.classList.add('border-blue-500', 'text-brand-600');

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

    // Variables globales para el modal de reemplazo y swap
    let currentReplaceContext = null;
    let selectedTeamForSwap = null;

    // Seleccionar equipo para intercambiar
    function selectTeamForSwap(categoriaId, matchIndex, slot, element) {
        // Si hay un equipo ya seleccionado, intercambiar
        if (selectedTeamForSwap) {
            // Obtener los inputs antes de intercambiar
            const input1 = selectedTeamForSwap.element.querySelector('.team-input');
            const input2 = element.querySelector('.team-input');

            if (!input1 || !input2) return; // No intercambiar si alguno es BYE

            // Guardar los valores originales
            const value1 = input1.value;
            const value2 = input2.value;

            // Intercambiar el contenido HTML
            const tempHTML = selectedTeamForSwap.element.innerHTML;
            selectedTeamForSwap.element.innerHTML = element.innerHTML;
            element.innerHTML = tempHTML;

            // Asegurarse de que los valores estén correctos después del intercambio
            const newInput1 = selectedTeamForSwap.element.querySelector('.team-input');
            const newInput2 = element.querySelector('.team-input');

            if (newInput1) newInput1.value = value2;
            if (newInput2) newInput2.value = value1;

            // Re-aplicar los onclick después del intercambio
            const savedMatch1 = selectedTeamForSwap.matchIndex;
            const savedSlot1 = selectedTeamForSwap.slot;

            selectedTeamForSwap.element.onclick = function() {
                selectTeamForSwap(categoriaId, savedMatch1, savedSlot1, this);
            };
            element.onclick = function() {
                selectTeamForSwap(categoriaId, matchIndex, slot, this);
            };

            // Limpiar selección
            selectedTeamForSwap.element.classList.remove('bg-blue-200', 'border-2', 'border-blue-500');
            selectedTeamForSwap = null;

            // Ocultar indicador
            const indicator = document.getElementById(`swap-indicator-${categoriaId}`);
            indicator.classList.add('hidden');
        } else {
            // Verificar que no sea un BYE
            if (element.querySelector('.team-input')) {
                // Seleccionar este equipo
                selectedTeamForSwap = {
                    categoriaId,
                    matchIndex,
                    slot,
                    element
                };

                // Marcar visualmente
                element.classList.add('bg-blue-200', 'border-2', 'border-blue-500');

                // Mostrar indicador
                const indicator = document.getElementById(`swap-indicator-${categoriaId}`);
                indicator.classList.remove('hidden');
            }
        }
    }

    // Cancelar selección de intercambio
    function cancelSwap(categoriaId) {
        if (selectedTeamForSwap) {
            selectedTeamForSwap.element.classList.remove('bg-blue-200', 'border-2', 'border-blue-500');
            selectedTeamForSwap = null;

            // Ocultar indicador
            const indicator = document.getElementById(`swap-indicator-${categoriaId}`);
            indicator.classList.add('hidden');
        }
    }

    // Mostrar modal para reemplazar equipo
    function showReplaceModal(categoriaId, matchIndex, slot, currentTeamId) {
        currentReplaceContext = {
            categoriaId,
            matchIndex,
            slot,
            currentTeamId
        };

        const modal = document.getElementById(`replace-modal-${categoriaId}`);
        modal.classList.remove('hidden');
    }

    // Cerrar modal de reemplazo
    function closeReplaceModal(categoriaId) {
        const modal = document.getElementById(`replace-modal-${categoriaId}`);
        modal.classList.add('hidden');
        currentReplaceContext = null;
    }

    // Reemplazar equipo
    function replaceTeam(categoriaId, newTeamId) {
        if (!currentReplaceContext) return;

        const { matchIndex, slot, currentTeamId } = currentReplaceContext;

        // Obtener datos del nuevo equipo
        const equiposNoClasificados = window.equiposNoClasificados[categoriaId];
        const newTeam = equiposNoClasificados.find(e => e.id === newTeamId);

        if (!newTeam) return;

        // Encontrar el slot correcto
        const match = document.querySelector(`#bracket-preview-${categoriaId} [data-match-index="${matchIndex}"]`);
        const teamSlot = match.querySelector(`[data-slot="${slot}"]`);

        // Crear nuevo HTML para el equipo
        const jugadoresText = newTeam.jugadores.map(j => j.nombre_completo).join(' / ');
        const newHTML = `
            <div class="flex items-center justify-between p-3">
                <div class="flex-1">
                    <div class="font-semibold text-gray-800">${newTeam.nombre}</div>
                    <div class="text-xs text-gray-500 mt-0.5">
                        <span class="inline-flex items-center px-2 py-0.5 rounded bg-gray-100 text-gray-800">
                            Reemplazo manual
                        </span>
                    </div>
                </div>
                <button type="button"
                        class="ml-2 px-2 py-1 text-xs bg-gray-200 hover:bg-gray-300 rounded z-10"
                        onclick="event.stopPropagation(); showReplaceModal(${categoriaId}, ${matchIndex}, '${slot}', ${newTeamId})"
                        title="Reemplazar este equipo">
                    Cambiar
                </button>
            </div>
            <input type="hidden"
                   class="team-input"
                   name="categorias[${categoriaId}][clasificados][]"
                   value="${newTeamId}">
        `;

        teamSlot.innerHTML = newHTML;

        // Re-aplicar el onclick al slot
        teamSlot.onclick = function() { selectTeamForSwap(categoriaId, matchIndex, slot, this); };

        // Cerrar modal
        closeReplaceModal(categoriaId);
    }

    // Cerrar modal al hacer click fuera
    document.addEventListener('click', function(e) {
        if (e.target.id && e.target.id.startsWith('replace-modal-')) {
            const categoriaId = e.target.id.replace('replace-modal-', '');
            closeReplaceModal(categoriaId);
        }
    });
</script>
@endpush
@endsection
