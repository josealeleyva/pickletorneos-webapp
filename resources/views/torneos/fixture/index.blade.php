@extends('layouts.dashboard')

@section('title', 'Fixture - ' . $torneo->nombre)
@section('page-title', 'Fixture del Torneo')

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
                    <span class="text-gray-700 font-medium">Fixture</span>
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
                    Fixture de partidos - {{ $torneo->categorias->count() }} {{ $torneo->categorias->count() === 1 ? 'categoría' : 'categorías' }}
                </p>
            </div>

            <div class="text-center">
                <div class="text-2xl sm:text-3xl font-bold text-brand-600">{{ $partidos->count() }}</div>
                <div class="text-xs text-gray-500">Partidos totales</div>
            </div>
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex flex-wrap gap-2 sm:gap-3">
        @if($torneo->estado === 'borrador')
        @if($partidos->isEmpty())
        <form action="{{ route('torneos.fixture.generar', $torneo) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Generar Fixture
            </button>
        </form>
        @else
        @if($partidos->whereNull('fecha_hora')->isNotEmpty())
        <form action="{{ route('torneos.fixture.programar', $torneo) }}" method="POST" class="inline">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Programar Fechas y Canchas
            </button>
        </form>
        @endif

        <form action="{{ route('torneos.fixture.resetear', $torneo) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de resetear el fixture? Se eliminarán todos los partidos.')">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-accent-600 hover:bg-accent-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Resetear Fixture
            </button>
        </form>
        @endif
        @endif

        @if(in_array($torneo->estado, ['activo', 'finalizado']) && $partidos->whereNotNull('fecha_hora')->where('estado', 'programado')->isNotEmpty())
        <form action="{{ route('torneos.fixture.notificar-todos', $torneo) }}" method="POST" class="inline" onsubmit="return confirm('¿Enviar notificaciones a todos los partidos programados?')">
            @csrf
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Notificar todos
            </button>
        </form>
        @endif

        @if(in_array($torneo->estado, ['activo', 'finalizado']) && $partidos->whereNotNull('fecha_hora')->where('estado', 'programado')->isNotEmpty())
        <button onclick="copiarFixture()" class="inline-flex items-center px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            Copiar Fixture
        </button>
        @endif
        @if($torneo->formato && $torneo->formato->tiene_grupos && $torneo->formato->tipo == 'borrador')
        <a href="{{ route('torneos.grupos.index', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
            </svg>
            Ver Grupos
        </a>
        @endif

        <a href="{{ route('torneos.show', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al Torneo
        </a>
    </div>

    @if($partidos->isEmpty())
    <!-- Estado vacío -->
    <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
        <svg class="mx-auto h-16 w-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Fixture no generado</h3>
        <p class="text-sm text-gray-500 mb-6">
            Genera el fixture para crear los partidos de la fase de grupos automáticamente usando el algoritmo de todos contra todos.
        </p>
    </div>
    @else
    <!-- Tabs de Categoría (Desktop: horizontal, Mobile: dropdown) -->
    <div class="bg-white rounded-lg shadow-sm">
        <!-- Desktop: Tabs horizontales -->
        <div class="hidden sm:block border-b border-gray-200">
            <nav class="flex overflow-x-auto" aria-label="Categorías">
                <button type="button" data-categoria-filter="all" class="categoria-filter-tab whitespace-nowrap py-4 px-6 border-b-2 border-brand-600 font-medium text-sm text-brand-600">
                    Todas ({{ $partidos->count() }})
                </button>
                @foreach($torneo->categorias as $categoria)
                @php
                $partidosCategoria = $partidos->filter(function($p) use ($categoria) {
                    // Para torneos con grupos, usar grupo->categoria_id
                    if ($p->grupo) {
                        return $p->grupo->categoria_id === $categoria->id;
                    }
                    // Para Liga, usar equipo->categoria_id
                    return ($p->equipo1 && $p->equipo1->categoria_id === $categoria->id)
                        || ($p->equipo2 && $p->equipo2->categoria_id === $categoria->id);
                });
                @endphp
                <button type="button" data-categoria-filter="categoria-{{ $categoria->id }}" class="categoria-filter-tab whitespace-nowrap py-4 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                    {{ $categoria->nombre }} ({{ $partidosCategoria->count() }})
                </button>
                @endforeach
            </nav>
        </div>

        <!-- Mobile: Selector dropdown -->
        <div class="sm:hidden p-4 border-b border-gray-200">
            <select id="categoria-filter-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500">
                <option value="all">Todas las Categorías ({{ $partidos->count() }})</option>
                @foreach($torneo->categorias as $categoria)
                @php
                $partidosCategoria = $partidos->filter(function($p) use ($categoria) {
                    // Para torneos con grupos, usar grupo->categoria_id
                    if ($p->grupo) {
                        return $p->grupo->categoria_id === $categoria->id;
                    }
                    // Para Liga, usar equipo->categoria_id
                    return ($p->equipo1 && $p->equipo1->categoria_id === $categoria->id)
                        || ($p->equipo2 && $p->equipo2->categoria_id === $categoria->id);
                });
                @endphp
                <option value="categoria-{{ $categoria->id }}">{{ $categoria->nombre }} ({{ $partidosCategoria->count() }})</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabs de Vistas (Grupos/Fechas/Posiciones) -->
    <!-- Tabs -->
    <div class="bg-white rounded-lg shadow-sm">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                <button onclick="switchTab('grupos')" id="tab-grupos" class="tab-button active px-4 sm:px-6 py-3 text-sm font-medium border-b-2 border-brand-600 text-brand-600 whitespace-nowrap">
                    @if($torneo->formato && $torneo->formato->tiene_grupos)
                        Por Grupo
                    @else
                        Por Categoría
                    @endif
                </button>
                <button onclick="switchTab('fechas')" id="tab-fechas" class="tab-button px-4 sm:px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    Por Fecha
                </button>
                <button onclick="switchTab('posiciones')" id="tab-posiciones" class="tab-button px-4 sm:px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 whitespace-nowrap">
                    Posiciones
                </button>
            </nav>
        </div>

        <!-- Vista por Grupos o Categorías -->
        <div id="content-grupos" class="tab-content p-4 sm:p-6">
            @if($torneo->formato && $torneo->formato->tiene_grupos)
                {{-- Vista para torneos con grupos --}}
                @foreach($grupos as $grupo)
            @php
            $partidosGrupo = $partidosPorGrupo[$grupo->nombre] ?? collect();
            @endphp

            <div class="grupo-section mb-6 last:mb-0" data-categoria="categoria-{{ $grupo->categoria_id }}">
                <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                    <span class="bg-brand-100 text-brand-700 px-3 py-1 rounded-lg">{{ $grupo->nombre }}</span>
                    @if($grupo->categoria)
                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">{{ $grupo->categoria->nombre }}</span>
                    @endif
                </h3>

                @if($partidosGrupo->isEmpty())
                <p class="text-sm text-gray-500 ml-4">No hay partidos generados para este grupo</p>
                @else
                <div class="space-y-2">
                    @foreach($partidosGrupo as $partido)
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 hover:bg-gray-100 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center justify-between sm:justify-start gap-4">
                                    <div class="text-sm font-medium text-gray-800 flex-1 sm:flex-none sm:w-48">{{ $partido->equipo1->nombre }}</div>
                                    <div class="text-gray-400 font-bold">vs</div>
                                    <div class="text-sm font-medium text-gray-800 flex-1 sm:flex-none sm:w-48 text-right sm:text-left">{{ $partido->equipo2->nombre }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                @if($partido->fecha_hora)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    {{ $partido->fecha_hora->format('d/m/Y H:i') }}
                                </div>
                                @endif

                                @if($partido->cancha)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $partido->cancha->complejo->nombre.', '.$partido->cancha->nombre }}
                                </div>
                                @endif

                                <!-- Resultado si ya está finalizado -->
                                @if($partido->estado === 'finalizado')
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($partido->juegos->sortBy('numero_juego') as $juego)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded font-semibold text-xs">
                                        {{ $juego->juegos_equipo1 }}-{{ $juego->juegos_equipo2 }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Botones de acción - mejor diseño mobile -->
                                <div class="flex flex-wrap gap-1">
                                    @if($torneo->estado === 'borrador' && $partido->fecha_hora)
                                    <button
                                        onclick="abrirModalEditar({{ $partido->id }}, '{{ $partido->fecha_hora->format('Y-m-d\TH:i') }}', {{ $partido->cancha_id }})"
                                        class="flex items-center text-brand-600 hover:text-brand-800 hover:bg-brand-50 px-2 py-1 rounded transition text-xs sm:text-sm"
                                        title="Editar partido">
                                        <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="hidden sm:inline">Editar</span>
                                    </button>
                                    @elseif(in_array($torneo->estado, ['en_curso', 'finalizado']) && $partido->estado !== 'finalizado')
                                    <button
                                        onclick="abrirModalResultado({{ $partido->id }}, '{{ $partido->equipo1->nombre }}', '{{ $partido->equipo2->nombre }}')"
                                        class="flex items-center text-green-600 hover:text-green-800 hover:bg-green-50 px-2 py-1 rounded transition text-xs whitespace-nowrap"
                                        title="Cargar resultado">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Cargar
                                    </button>
                                    @endif

                                    @if($partido->fecha_hora && in_array($torneo->estado, ['en_curso', 'finalizado']) && $partido->estado !== 'finalizado')
                                    <button
                                        onclick="enviarNotificaciones({{ $partido->id }})"
                                        class="flex items-center text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded transition text-xs whitespace-nowrap"
                                        title="Enviar notificaciones">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Notificar
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
                @endforeach
            @else
                {{-- Vista para torneos Liga (por categoría) --}}
                @foreach($torneo->categorias as $categoria)
                    @php
                    $partidosCategoria = $partidos->filter(function($p) use ($categoria) {
                        return ($p->equipo1 && $p->equipo1->categoria_id === $categoria->id)
                            || ($p->equipo2 && $p->equipo2->categoria_id === $categoria->id);
                    });
                    @endphp

                    @if($partidosCategoria->isNotEmpty())
                    <div class="grupo-section mb-6 last:mb-0" data-categoria="categoria-{{ $categoria->id }}">
                        <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                            <span class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg">{{ $categoria->nombre }}</span>
                        </h3>

                        <div class="space-y-2">
                            @foreach($partidosCategoria as $partido)
                            <div class="bg-gray-50 rounded-lg p-3 sm:p-4 hover:bg-gray-100 transition">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between sm:justify-start gap-4">
                                            <div class="text-sm font-medium text-gray-800 flex-1 sm:flex-none sm:w-48">{{ $partido->equipo1->nombre }}</div>
                                            <div class="text-gray-400 font-bold">vs</div>
                                            <div class="text-sm font-medium text-gray-800 flex-1 sm:flex-none sm:w-48 text-right sm:text-left">{{ $partido->equipo2->nombre }}</div>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-3 text-xs text-gray-500">
                                        @if($partido->fecha_hora)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $partido->fecha_hora->format('d/m/Y H:i') }}
                                        </div>
                                        @endif

                                        @if($partido->cancha)
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            </svg>
                                            {{ $partido->cancha->complejo->nombre.', '.$partido->cancha->nombre }}
                                        </div>
                                        @endif

                                        @if($partido->estado === 'finalizado')
                                        <div class="flex gap-1 flex-wrap">
                                            @foreach($partido->juegos->sortBy('numero_juego') as $juego)
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded font-semibold text-xs">
                                                {{ $juego->juegos_equipo1 }}-{{ $juego->juegos_equipo2 }}
                                            </span>
                                            @endforeach
                                        </div>
                                        @endif

                                        <div class="flex flex-wrap gap-1">
                                            @if($torneo->estado === 'borrador' && $partido->fecha_hora)
                                            <button
                                                onclick="abrirModalEditar({{ $partido->id }}, '{{ $partido->fecha_hora->format('Y-m-d\TH:i') }}', {{ $partido->cancha_id }})"
                                                class="flex items-center text-brand-600 hover:text-brand-800 hover:bg-brand-50 px-2 py-1 rounded transition text-xs sm:text-sm"
                                                title="Editar partido">
                                                <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                <span class="hidden sm:inline">Editar</span>
                                            </button>
                                            @elseif(in_array($torneo->estado, ['en_curso', 'finalizado']) && $partido->estado !== 'finalizado')
                                            <button
                                                onclick="abrirModalResultado({{ $partido->id }}, '{{ $partido->equipo1->nombre }}', '{{ $partido->equipo2->nombre }}')"
                                                class="flex items-center text-green-600 hover:text-green-800 hover:bg-green-50 px-2 py-1 rounded transition text-xs whitespace-nowrap"
                                                title="Cargar resultado">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                                Cargar
                                            </button>
                                            @endif

                                            @if($partido->fecha_hora && in_array($torneo->estado, ['en_curso', 'finalizado']) && $partido->estado !== 'finalizado')
                                            <button
                                                onclick="enviarNotificaciones({{ $partido->id }})"
                                                class="flex items-center text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded transition text-xs whitespace-nowrap"
                                                title="Enviar notificaciones">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                Notificar
                                            </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                @endforeach
            @endif
        </div>

        <!-- Vista por Fechas -->
        <div id="content-fechas" class="tab-content hidden p-4 sm:p-6">
            @if($partidosPorFecha->isEmpty())
            <p class="text-sm text-gray-500 text-center py-8">
                Los partidos aún no tienen fechas programadas.
                <br>Usa el botón "Programar Fechas y Canchas" para asignarlas automáticamente.
            </p>
            @else
            @foreach($partidosPorFecha as $fecha => $partidosFecha)
            <div class="mb-6 last:mb-0">
                <h3 class="text-lg font-bold text-gray-800 mb-3">
                    {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}
                </h3>

                <div class="space-y-2">
                    @foreach($partidosFecha->sortBy('fecha_hora') as $partido)
                    <div class="bg-gray-50 rounded-lg p-3 sm:p-4 hover:bg-gray-100 transition">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex-1">
                                <div class="flex items-center justify-between sm:justify-start gap-4">
                                    <div class="text-sm font-medium text-gray-800 flex-1 sm:flex-none sm:w-48">{{ $partido->equipo1->nombre }}</div>
                                    <div class="text-gray-400 font-bold">vs</div>
                                    <div class="text-sm font-medium text-gray-800 flex-1 sm:flex-none sm:w-48 text-right sm:text-left">{{ $partido->equipo2->nombre }}</div>
                                </div>
                            </div>

                            <div class="flex items-center gap-3 text-xs text-gray-500">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $partido->fecha_hora->format('H:i') }}
                                </div>

                                @if($partido->cancha)
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    {{ $partido->cancha->complejo->nombre.', '.$partido->cancha->nombre }}
                                </div>
                                @endif

                                @if($partido->grupo)
                                <span class="bg-brand-100 text-brand-700 px-2 py-0.5 rounded text-xs">{{ $partido->grupo->nombre }}</span>
                                @endif

                                <!-- Resultado si ya está finalizado -->
                                @if($partido->estado === 'finalizado')
                                <div class="flex gap-1 flex-wrap">
                                    @foreach($partido->juegos->sortBy('numero_juego') as $juego)
                                    <span class="bg-green-100 text-green-700 px-2 py-1 rounded font-semibold text-xs">
                                        {{ $juego->juegos_equipo1 }}-{{ $juego->juegos_equipo2 }}
                                    </span>
                                    @endforeach
                                </div>
                                @endif

                                <!-- Botones de acción -->
                                <div class="flex flex-wrap gap-1">
                                    @if($torneo->estado === 'borrador' && $partido->fecha_hora)
                                    <button
                                        onclick="abrirModalEditar({{ $partido->id }}, '{{ $partido->fecha_hora->format('Y-m-d\TH:i') }}', {{ $partido->cancha_id }})"
                                        class="flex items-center text-brand-600 hover:text-brand-800 hover:bg-brand-50 px-2 py-1 rounded transition text-xs sm:text-sm"
                                        title="Editar partido">
                                        <svg class="w-4 h-4 sm:mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        <span class="hidden sm:inline">Editar</span>
                                    </button>
                                    @elseif(in_array($torneo->estado, ['en_curso', 'finalizado']) && $partido->estado !== 'finalizado')
                                    <button
                                        onclick="abrirModalResultado({{ $partido->id }}, '{{ $partido->equipo1->nombre }}', '{{ $partido->equipo2->nombre }}')"
                                        class="flex items-center text-green-600 hover:text-green-800 hover:bg-green-50 px-2 py-1 rounded transition text-xs whitespace-nowrap"
                                        title="Cargar resultado">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        Cargar
                                    </button>
                                    @endif

                                    @if($partido->fecha_hora && in_array($torneo->estado, ['en_curso', 'finalizado']) && $partido->estado !== 'finalizado')
                                    <button
                                        onclick="enviarNotificaciones({{ $partido->id }})"
                                        class="flex items-center text-blue-600 hover:text-blue-800 hover:bg-blue-50 px-2 py-1 rounded transition text-xs whitespace-nowrap"
                                        title="Enviar notificaciones">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                        </svg>
                                        Notificar
                                    </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endforeach
            @endif
        </div>

        <!-- Vista de Posiciones -->
        <div id="content-posiciones" class="tab-content hidden p-4 sm:p-6">
            @if(!empty($tablaPosiciones))
            @php
            $posicionesPorCategoria = collect($tablaPosiciones)->groupBy('categoria_id');
            $esLiga = $torneo->formato && $torneo->formato->esLiga();
            @endphp

            @foreach($posicionesPorCategoria as $categoriaId => $gruposCategoria)
            <div class="posicion-categoria mb-8 last:mb-0" data-categoria="categoria-{{ $categoriaId }}">
                <!-- Título de categoría -->
                @php
                $primerGrupo = $gruposCategoria->first();
                $categoriaNombre = $primerGrupo['categoria_nombre'];
                $campeonId = $primerGrupo['campeon_id'] ?? null;
                @endphp
                <div class="mb-4 pb-2 border-b-2 border-brand-200">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-bold text-brand-700">Categoría {{ $categoriaNombre }}</h2>

                        @if($esLiga && $campeonId)
                            @php
                            $equipoCampeon = \App\Models\Equipo::find($campeonId);
                            @endphp
                            <div class="flex items-center gap-2 bg-yellow-100 px-4 py-2 rounded-lg">
                                <svg class="w-5 h-5 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                                <span class="font-bold text-yellow-800">Campeón: {{ $equipoCampeon->nombre }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Tablas de cada grupo de esta categoría -->
                @foreach($gruposCategoria as $grupoData)
                <div class="mb-6 last:mb-0">
                    @if(!$esLiga)
                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                        <span class="bg-brand-100 text-brand-700 px-3 py-1 rounded-lg">{{ $grupoData['grupo_nombre'] }}</span>
                    </h3>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Equipo</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PJ</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PG</th>
                                    @if($torneo->deporte->permiteEmpates())
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PE</th>
                                    @endif
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">PP</th>
                                    @if($torneo->deporte->usaSets())
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SG</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">SP</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">DifS</th>
                                    @endif
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $torneo->deporte->usaSets() ? 'GF' : 'GF' }}</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $torneo->deporte->usaSets() ? 'GC' : 'GC' }}</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $torneo->deporte->usaSets() ? 'DifG' : 'DifG' }}</th>
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider font-bold">Pts</th>
                                    @if($esLiga && $torneo->estado === 'activo')
                                    <th class="px-3 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Acción</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($grupoData['posiciones'] as $index => $pos)
                                @php
                                $cantidadClasifican = $grupoData['cantidad_clasifican'] ?? 0;
                                $clasificaDirecto = $cantidadClasifican > 0 && $index < $cantidadClasifican;
                                $esPrimero = $index === 0;
                                $esCampeon = $campeonId && $pos['equipo']->id === $campeonId;
                                @endphp
                                <tr class="hover:bg-gray-50 {{ $clasificaDirecto ? 'bg-green-50' : '' }} {{ $esCampeon ? 'bg-yellow-50 border-l-4 border-yellow-500' : '' }}">
                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $index + 1 }}
                                        @if($clasificaDirecto)
                                        <span class="ml-1 text-green-600" title="Clasifica">✓</span>
                                        @endif
                                        @if($esCampeon)
                                        <span class="ml-1 text-yellow-600" title="Campeón">🏆</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 {{ $esCampeon ? 'font-bold' : '' }}">
                                            {{ $pos['equipo']->nombre }}
                                        </div>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['pj'] }}</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['pg'] }}</td>
                                    @if($torneo->deporte->permiteEmpates())
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['pe'] ?? 0 }}</td>
                                    @endif
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['pp'] }}</td>
                                    @if($torneo->deporte->usaSets())
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['sg'] }}</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['sp'] }}</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="font-medium {{ $pos['diferencia_sets'] > 0 ? 'text-green-600' : ($pos['diferencia_sets'] < 0 ? 'text-red-600' : 'text-gray-900') }}">
                                            {{ $pos['diferencia_sets'] > 0 ? '+' : '' }}{{ $pos['diferencia_sets'] }}
                                        </span>
                                    </td>
                                    @endif
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['pf'] }}</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-gray-900 text-center">{{ $pos['pc'] }}</td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm text-center">
                                        <span class="font-medium {{ $pos['diferencia_puntos'] > 0 ? 'text-green-600' : ($pos['diferencia_puntos'] < 0 ? 'text-red-600' : 'text-gray-900') }}">
                                            {{ $pos['diferencia_puntos'] > 0 ? '+' : '' }}{{ $pos['diferencia_puntos'] }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-center">{{ $pos['puntos'] }}</td>
                                    @if($esLiga && $torneo->estado === 'activo')
                                    <td class="px-3 py-4 whitespace-nowrap text-center">
                                        @if(!$esCampeon)
                                        <button
                                            onclick="marcarCampeon({{ $categoriaId }}, {{ $pos['equipo']->id }}, '{{ $pos['equipo']->nombre }}')"
                                            class="inline-flex items-center px-2 py-1 bg-yellow-500 hover:bg-yellow-600 text-white text-xs font-medium rounded transition"
                                            title="Marcar como campeón">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        </button>
                                        @else
                                        <span class="text-xs text-yellow-600 font-semibold">Campeón</span>
                                        @endif
                                    </td>
                                    @endif
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
            @endforeach
            @else
            <p class="text-sm text-gray-500 text-center py-8">
                Aún no hay resultados cargados para calcular posiciones.
            </p>
            @endif
        </div>
    </div>
    @endif
</div>

<!-- Modal Editar Partido -->
@if($torneo->estado === 'borrador' && $partidos->isNotEmpty())
<div id="modalEditarPartido" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-md w-full p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Editar Partido</h3>
            <button onclick="cerrarModalEditar()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <form id="formEditarPartido" onsubmit="guardarPartido(event)">
            <input type="hidden" id="partido_id">

            <div class="space-y-4">
                <div>
                    <label for="fecha_hora" class="block text-sm font-medium text-gray-700 mb-1">Fecha y Hora</label>
                    <input
                        type="datetime-local"
                        id="fecha_hora"
                        name="fecha_hora"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                </div>

                <div>
                    <label for="cancha_id" class="block text-sm font-medium text-gray-700 mb-1">Cancha</label>
                    <select
                        id="cancha_id"
                        name="cancha_id"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
                        <option value="">Seleccionar cancha</option>
                        @foreach($canchas as $cancha)
                        <option value="{{ $cancha->id }}">{{ $cancha->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button
                    type="button"
                    onclick="cerrarModalEditar()"
                    class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                    Cancelar
                </button>
                <button
                    type="submit"
                    class="flex-1 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-medium rounded-lg transition">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Modal Cargar Resultado -->
@if(in_array($torneo->estado, ['en_curso', 'finalizado']) && $partidos->isNotEmpty())
<div id="modalCargarResultado" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg max-w-xl w-full p-6 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Cargar Resultado</h3>
            <button onclick="cerrarModalResultado()" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <input type="hidden" id="resultado_partido_id">

        <div class="mb-4 text-center bg-gray-50 p-3 rounded-lg">
            <span id="equipo1_nombre" class="font-semibold text-gray-900"></span>
            <span class="mx-2 text-gray-500">vs</span>
            <span id="equipo2_nombre" class="font-semibold text-gray-900"></span>
        </div>

        <!-- Puntos totales (acumulados) -->
        <div class="mb-4 bg-blue-50 p-4 rounded-lg">
            <div class="text-center mb-2 text-sm text-gray-600">{{ $torneo->deporte->esFutbol() ? 'Goles' : 'Games' }} Acumulados</div>
            <div class="flex items-center justify-center gap-4">
                <div class="text-center">
                    <div id="puntos_acum_equipo1" class="text-3xl font-bold text-blue-600">0</div>
                    <div class="text-xs text-gray-500" id="puntos_label1"></div>
                </div>
                <div class="text-2xl text-gray-400">-</div>
                <div class="text-center">
                    <div id="puntos_acum_equipo2" class="text-3xl font-bold text-blue-600">0</div>
                    <div class="text-xs text-gray-500" id="puntos_label2"></div>
                </div>
            </div>
        </div>

        <!-- Juegos cargados -->
        <div id="juegos-lista" class="mb-4 space-y-2">
            <!-- Los juegos se agregan dinámicamente aquí -->
        </div>

        <!-- Formulario para agregar juego -->
        <form id="formAgregarJuego" onsubmit="agregarJuego(event)" class="mb-4">
            <div class="bg-gray-50 p-4 rounded-lg">
                <div class="text-sm font-medium text-gray-700 mb-2">Agregar {{ $torneo->deporte->esFutbol() ? 'Goles' : 'Games' }}</div>
                <div class="grid grid-cols-2 gap-4 mb-3">
                    <div>
                        <label for="juegos_equipo1" class="block text-xs text-gray-600 mb-1" id="label_juego1"></label>
                        <input
                            type="number"
                            id="juegos_equipo1"
                            name="juegos_equipo1"
                            min="0"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-xl font-bold">
                    </div>

                    <div>
                        <label for="juegos_equipo2" class="block text-xs text-gray-600 mb-1" id="label_juego2"></label>
                        <input
                            type="number"
                            id="juegos_equipo2"
                            name="juegos_equipo2"
                            min="0"
                            required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-center text-xl font-bold">
                    </div>
                </div>
                <button
                    type="submit"
                    class="w-full px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-medium rounded-lg transition text-sm">
                    Agregar
                </button>
            </div>
        </form>

        <!-- Botón para finalizar partido -->
        <div class="flex gap-3">
            <button
                type="button"
                onclick="cerrarModalResultado()"
                class="flex-1 px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium rounded-lg transition">
                Cancelar
            </button>
            <button
                type="button"
                onclick="finalizarPartido()"
                id="btnFinalizar"
                disabled
                class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition disabled:opacity-50 disabled:cursor-not-allowed">
                Finalizar Partido
            </button>
        </div>
    </div>
</div>
@endif

@push('scripts')
<script>
    // Variables para filtrado por categoría
    const grupoSections = document.querySelectorAll('.grupo-section');
    const posicionCategorias = document.querySelectorAll('.posicion-categoria');
    const categoriaFilterTabs = document.querySelectorAll('.categoria-filter-tab');
    const categoriaFilterSelect = document.getElementById('categoria-filter-select');
    let categoriaActual = 'all';

    // Función para filtrar por categoría
    function filtrarPorCategoria(categoriaId) {
        categoriaActual = categoriaId;

        // Filtrar secciones de grupos
        grupoSections.forEach(section => {
            if (categoriaId === 'all' || section.dataset.categoria === categoriaId) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        });

        // Filtrar secciones de posiciones
        posicionCategorias.forEach(section => {
            if (categoriaId === 'all' || section.dataset.categoria === categoriaId) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        });

        // Actualizar tabs (desktop)
        categoriaFilterTabs.forEach(tab => {
            if (tab.dataset.categoriaFilter === categoriaId) {
                tab.classList.remove('border-transparent', 'text-gray-500');
                tab.classList.add('border-brand-600', 'text-brand-600');
            } else {
                tab.classList.remove('border-brand-600', 'text-brand-600');
                tab.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
            }
        });

        // Actualizar select (mobile)
        if (categoriaFilterSelect) {
            categoriaFilterSelect.value = categoriaId;
        }
    }

    // Event listeners para tabs desktop
    categoriaFilterTabs.forEach(tab => {
        tab.addEventListener('click', function() {
            filtrarPorCategoria(this.dataset.categoriaFilter);
        });
    });

    // Event listener para selector mobile
    if (categoriaFilterSelect) {
        categoriaFilterSelect.addEventListener('change', function() {
            filtrarPorCategoria(this.value);
        });
    }

    function switchTab(tab) {
        // Remover active de todos los tabs
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'border-brand-600', 'text-brand-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        // Ocultar todos los contenidos
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        // Activar tab seleccionado
        document.getElementById('tab-' + tab).classList.add('active', 'border-brand-600', 'text-brand-600');
        document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');

        // Mostrar contenido seleccionado
        document.getElementById('content-' + tab).classList.remove('hidden');

        // Aplicar filtro de categoría actual al cambiar de tab
        filtrarPorCategoria(categoriaActual);
    }

    function abrirModalEditar(partidoId, fechaHora, canchaId) {
        document.getElementById('partido_id').value = partidoId;
        document.getElementById('fecha_hora').value = fechaHora;
        document.getElementById('cancha_id').value = canchaId;
        document.getElementById('modalEditarPartido').classList.remove('hidden');
    }

    function cerrarModalEditar() {
        document.getElementById('modalEditarPartido').classList.add('hidden');
    }

    async function guardarPartido(event) {
        event.preventDefault();

        const partidoId = document.getElementById('partido_id').value;
        const fechaHora = document.getElementById('fecha_hora').value;
        const canchaId = document.getElementById('cancha_id').value;

        try {
            const response = await fetch(`{{ route('torneos.fixture.actualizar', ['torneo' => $torneo->id, 'partido' => '__PARTIDO_ID__']) }}`.replace('__PARTIDO_ID__', partidoId), {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    fecha_hora: fechaHora,
                    cancha_id: canchaId
                })
            });

            const data = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                alert(data.error || 'Error al actualizar el partido');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al actualizar el partido. Por favor intenta nuevamente.');
        }
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalEditarPartido')?.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalEditar();
        }
    });

    // Variables globales para el modal de resultado
    let juegosDelPartido = [];
    let puntosEquipo1 = 0;
    let puntosEquipo2 = 0;
    let nombreEquipo1 = '';
    let nombreEquipo2 = '';

    // Funciones para Modal de Resultado
    function abrirModalResultado(partidoId, equipo1Nombre, equipo2Nombre) {
        // Resetear variables
        juegosDelPartido = [];
        puntosEquipo1 = 0;
        puntosEquipo2 = 0;
        nombreEquipo1 = equipo1Nombre;
        nombreEquipo2 = equipo2Nombre;

        // Configurar modal
        document.getElementById('resultado_partido_id').value = partidoId;
        document.getElementById('equipo1_nombre').textContent = equipo1Nombre;
        document.getElementById('equipo2_nombre').textContent = equipo2Nombre;
        document.getElementById('puntos_label1').textContent = equipo1Nombre;
        document.getElementById('puntos_label2').textContent = equipo2Nombre;
        document.getElementById('label_juego1').textContent = 'Juegos ' + equipo1Nombre;
        document.getElementById('label_juego2').textContent = 'Juegos ' + equipo2Nombre;

        // Limpiar inputs y lista
        document.getElementById('juegos_equipo1').value = '';
        document.getElementById('juegos_equipo2').value = '';
        document.getElementById('juegos-lista').innerHTML = '';

        // Actualizar puntos acumulados
        actualizarPuntosAcumulados();

        // Deshabilitar botón finalizar
        document.getElementById('btnFinalizar').disabled = true;

        document.getElementById('modalCargarResultado').classList.remove('hidden');
    }

    function cerrarModalResultado() {
        document.getElementById('modalCargarResultado').classList.add('hidden');
    }

    function actualizarPuntosAcumulados() {
        document.getElementById('puntos_acum_equipo1').textContent = puntosEquipo1;
        document.getElementById('puntos_acum_equipo2').textContent = puntosEquipo2;
    }

    function agregarJuego(event) {
        event.preventDefault();

        const juegos1 = parseInt(document.getElementById('juegos_equipo1').value);
        const juegos2 = parseInt(document.getElementById('juegos_equipo2').value);

        // Sumar puntos
        puntosEquipo1 += juegos1;
        puntosEquipo2 += juegos2;

        // Agregar juego a la lista
        juegosDelPartido.push({
            juegos_equipo1: juegos1,
            juegos_equipo2: juegos2
        });

        // Actualizar UI
        actualizarPuntosAcumulados();
        agregarJuegoALista(juegos1, juegos2);

        // Limpiar inputs
        document.getElementById('juegos_equipo1').value = '';
        document.getElementById('juegos_equipo2').value = '';

        // Habilitar botón finalizar
        document.getElementById('btnFinalizar').disabled = false;
    }

    function agregarJuegoALista(juegos1, juegos2) {
        const juegoHtml = `
        <div class="bg-white border border-gray-200 rounded-lg p-3 flex items-center justify-between">
            <div class="flex items-center gap-4 flex-1">
                <span class="text-sm text-gray-500">Juego ${juegosDelPartido.length}</span>
                <span class="font-bold text-lg">${juegos1} - ${juegos2}</span>
            </div>
            <button onclick="eliminarJuego(${juegosDelPartido.length - 1})" class="text-red-600 hover:text-red-800 p-1">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
        document.getElementById('juegos-lista').insertAdjacentHTML('beforeend', juegoHtml);
    }

    function eliminarJuego(index) {
        const juego = juegosDelPartido[index];

        // Restar puntos
        puntosEquipo1 -= juego.juegos_equipo1;
        puntosEquipo2 -= juego.juegos_equipo2;

        // Eliminar juego del array
        juegosDelPartido.splice(index, 1);

        // Recargar lista
        document.getElementById('juegos-lista').innerHTML = '';
        juegosDelPartido.forEach((j, i) => {
            agregarJuegoALista(j.juegos_equipo1, j.juegos_equipo2);
        });

        // Actualizar puntos acumulados
        actualizarPuntosAcumulados();

        // Deshabilitar botón si no hay juegos
        if (juegosDelPartido.length === 0) {
            document.getElementById('btnFinalizar').disabled = true;
        }
    }

    async function finalizarPartido() {
        if (juegosDelPartido.length === 0) {
            alert('Debes agregar al menos un juego.');
            return;
        }

        // Confirmar antes de finalizar
        if (!confirm('⚠️ ¿Estás seguro de finalizar este partido?\n\nPor favor verifica que los resultados sean correctos antes de continuar. No se podrán hacer modificaciones luego.')) {
            return;
        }

        const partidoId = document.getElementById('resultado_partido_id').value;

        try {
            const response = await fetch(`{{ route('torneos.fixture.resultado', ['torneo' => $torneo->id, 'partido' => '__PARTIDO_ID__']) }}`.replace('__PARTIDO_ID__', partidoId), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    juegos: juegosDelPartido
                })
            });

            const data = await response.json();

            if (response.ok) {
                window.location.reload();
            } else {
                alert(data.error || 'Error al cargar el resultado');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el resultado. Por favor intenta nuevamente.');
        }
    }

    // Cerrar modal de resultado al hacer click fuera
    document.getElementById('modalCargarResultado')?.addEventListener('click', function(e) {
        if (e.target === this) {
            cerrarModalResultado();
        }
    });

    // Enviar notificaciones
    async function enviarNotificaciones(partidoId) {
        if (!confirm('¿Deseas enviar notificaciones por email a todos los jugadores de este partido?')) {
            return;
        }

        try {
            const response = await fetch(`/torneos/{{ $torneo->id }}/fixture/notificar`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    partido_id: partidoId
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

    // Marcar campeón de categoría (Liga)
    async function marcarCampeon(categoriaId, equipoId, equipoNombre) {
        if (!confirm(`¿Confirmas que "${equipoNombre}" es el campeón de esta categoría?`)) {
            return;
        }

        try {
            const response = await fetch(`/torneos/{{ $torneo->id }}/fixture/marcar-campeon`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({
                    categoria_id: categoriaId,
                    equipo_id: equipoId
                })
            });

            const data = await response.json();

            if (data.success) {
                window.location.reload();
            } else {
                alert(data.error || 'Error al marcar el campeón');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al marcar el campeón. Por favor intenta nuevamente.');
        }
    }

    // Copiar fixture para WhatsApp
    async function copiarFixture() {
        const torneoNombre = "{{ $torneo->nombre }}";
        const complejoNombre = "{{ $torneo->complejo->nombre }}";

        // Obtener partidos visibles según filtro actual
        let partidosVisibles = [];

        // Detectar qué vista está activa
        const vistaActiva = document.querySelector('.tab-content:not(.hidden)');
        const vistaId = vistaActiva?.id;

        if (vistaId === 'content-grupos') {
            // Vista por grupos
            const gruposSections = vistaActiva.querySelectorAll('.grupo-section:not(.hidden)');
            gruposSections.forEach(grupoSection => {
                const grupoNombre = grupoSection.querySelector('h3 .bg-brand-100')?.textContent.trim();
                const categoriaNombre = grupoSection.querySelector('.bg-purple-100')?.textContent.trim() || '';

                const partidosContainer = grupoSection.querySelector('.space-y-2');
                if (!partidosContainer) return;

                const partidosElements = partidosContainer.querySelectorAll(':scope > .bg-gray-50');
                partidosElements.forEach(partidoEl => {
                    // Obtener nombres de equipos
                    const equiposContainer = partidoEl.querySelector('.flex-1 .flex.items-center');
                    if (!equiposContainer) return;

                    const equiposArray = equiposContainer.querySelectorAll('.text-sm.font-medium.text-gray-800');
                    const equipo1 = equiposArray[0]?.textContent?.trim();
                    const equipo2 = equiposArray[1]?.textContent?.trim();

                    // Obtener detalles (fecha, hora, cancha)
                    let fechaHora = 'Sin programar';
                    let cancha = '';

                    const detallesDiv = partidoEl.querySelector('.flex.items-center.gap-3.text-xs.text-gray-500');
                    if (detallesDiv) {
                        // Recorrer hijos directos buscando los elementos con SVG
                        Array.from(detallesDiv.children).forEach(child => {
                            // Verificar si es un div con flex y items-center
                            if (child.classList.contains('flex') && child.classList.contains('items-center')) {
                                const svg = child.querySelector('svg');
                                if (svg) {
                                    // Clonar el elemento
                                    const clone = child.cloneNode(true);
                                    // Remover el SVG del clon
                                    const svgClone = clone.querySelector('svg');
                                    if (svgClone) {
                                        svgClone.remove();
                                    }
                                    const texto = clone.textContent.trim();

                                    // Determinar si es fecha/hora o cancha por el contenido del path del SVG original
                                    const paths = svg.querySelectorAll('path');
                                    let esFecha = false;
                                    let esCancha = false;

                                    paths.forEach(path => {
                                        const d = path.getAttribute('d') || '';
                                        if (d.includes('M8 7V3')) {
                                            esFecha = true;
                                        }
                                        if (d.includes('M17.657')) {
                                            esCancha = true;
                                        }
                                    });

                                    if (esFecha && texto) {
                                        fechaHora = texto;
                                    } else if (esCancha && texto) {
                                        cancha = texto;
                                    }
                                }
                            }
                        });
                    }

                    if (equipo1 && equipo2) {
                        partidosVisibles.push({
                            categoria: categoriaNombre,
                            grupo: grupoNombre,
                            equipo1,
                            equipo2,
                            fechaHora,
                            cancha
                        });
                    }
                });
            });
        } else if (vistaId === 'content-fechas') {
            // Vista por fechas
            const fechasSections = vistaActiva.querySelectorAll(':scope > .mb-6');
            fechasSections.forEach(fechaSection => {
                const fechaTitulo = fechaSection.querySelector('h3')?.textContent.trim();

                const partidosContainer = fechaSection.querySelector('.space-y-2');
                if (!partidosContainer) return;

                const partidosElements = partidosContainer.querySelectorAll(':scope > .bg-gray-50');
                partidosElements.forEach(partidoEl => {
                    // Obtener nombres de equipos
                    const equiposContainer = partidoEl.querySelector('.flex-1 .flex.items-center');
                    if (!equiposContainer) return;

                    const equiposArray = equiposContainer.querySelectorAll('.text-sm.font-medium.text-gray-800');
                    const equipo1 = equiposArray[0]?.textContent?.trim();
                    const equipo2 = equiposArray[1]?.textContent?.trim();

                    // Obtener hora, cancha y grupo
                    let hora = '';
                    let cancha = '';
                    let grupo = '';

                    const detallesDiv = partidoEl.querySelector('.flex.items-center.gap-3.text-xs.text-gray-500');
                    if (detallesDiv) {
                        // Recorrer hijos directos buscando los elementos con SVG
                        Array.from(detallesDiv.children).forEach(child => {
                            // Verificar si es un div con flex y items-center
                            if (child.classList.contains('flex') && child.classList.contains('items-center')) {
                                const svg = child.querySelector('svg');
                                if (svg) {
                                    // Clonar el elemento
                                    const clone = child.cloneNode(true);
                                    // Remover el SVG del clon
                                    const svgClone = clone.querySelector('svg');
                                    if (svgClone) {
                                        svgClone.remove();
                                    }
                                    const texto = clone.textContent.trim();

                                    // Determinar si es hora o cancha por el contenido del path del SVG original
                                    const paths = svg.querySelectorAll('path');
                                    let esHora = false;
                                    let esCancha = false;

                                    paths.forEach(path => {
                                        const d = path.getAttribute('d') || '';
                                        if (d.includes('M12 8v4')) {
                                            esHora = true;
                                        }
                                        if (d.includes('M17.657')) {
                                            esCancha = true;
                                        }
                                    });

                                    if (esHora && texto) {
                                        hora = texto;
                                    } else if (esCancha && texto) {
                                        cancha = texto;
                                    }
                                }
                            }
                        });

                        // Grupo está en un span
                        const grupoSpan = detallesDiv.querySelector('.bg-brand-100.text-brand-700');
                        if (grupoSpan) {
                            grupo = grupoSpan.textContent.trim();
                        }
                    }

                    if (equipo1 && equipo2) {
                        partidosVisibles.push({
                            fecha: fechaTitulo,
                            equipo1,
                            equipo2,
                            hora,
                            cancha,
                            grupo
                        });
                    }
                });
            });
        }

        if (partidosVisibles.length === 0) {
            alert('No hay partidos programados para copiar');
            return;
        }

        // Generar texto formateado
        let textoFixture = `📅 *FIXTURE - ${torneoNombre}*\n`;
        textoFixture += `📍 ${complejoNombre}\n`;

        if (vistaId === 'content-grupos') {
            // Agrupar por categoría y grupo
            const porCategoriaGrupo = {};
            partidosVisibles.forEach(p => {
                const key = `${p.categoria}|${p.grupo}`;
                if (!porCategoriaGrupo[key]) porCategoriaGrupo[key] = [];
                porCategoriaGrupo[key].push(p);
            });

            Object.keys(porCategoriaGrupo).forEach(key => {
                const [categoria, grupo] = key.split('|');
                const header = [categoria, grupo].filter(Boolean).join(' | ');
                textoFixture += `\n🏅 *${header}*\n`;

                porCategoriaGrupo[key].forEach(p => {
                    const fechaCorta = p.fechaHora.replace(/(\d{2}\/\d{2})\/\d{4}/, '$1');
                    let linea = `⚔️ ${fechaCorta}`;
                    if (p.cancha) linea += ` | ${p.cancha}`;
                    linea += ` | ${p.equipo1} vs ${p.equipo2}`;
                    textoFixture += linea + '\n';
                });
            });
        } else {
            // Agrupar por fecha — el título de sección ya es la fecha, acortarla también
            const porFecha = {};
            partidosVisibles.forEach(p => {
                if (!porFecha[p.fecha]) porFecha[p.fecha] = [];
                porFecha[p.fecha].push(p);
            });

            Object.keys(porFecha).forEach(fecha => {
                const fechaTituloCorta = fecha.replace(/(\d{2}\/\d{2})\/\d{4}/, '$1');
                textoFixture += `\n📅 *${fechaTituloCorta}*\n`;

                porFecha[fecha].forEach(p => {
                    let linea = `⚔️ ${p.hora}`;
                    if (p.cancha) linea += ` | ${p.cancha}`;
                    linea += ` | ${p.equipo1} vs ${p.equipo2}`;
                    if (p.grupo) linea += ` (${p.grupo})`;
                    textoFixture += linea + '\n';
                });
            });
        }

        // Copiar al portapapeles
        try {
            await navigator.clipboard.writeText(textoFixture);
            alert('✅ Fixture copiado al portapapeles!\nAhora puedes pegarlo en WhatsApp.');
        } catch (err) {
            // Fallback para navegadores que no soportan clipboard API
            const textarea = document.createElement('textarea');
            textarea.value = textoFixture;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                alert('✅ Fixture copiado al portapapeles!\nAhora puedes pegarlo en WhatsApp.');
            } catch (e) {
                alert('❌ No se pudo copiar automáticamente. Por favor, copia manualmente el texto:\n\n' + textoFixture);
            }
            document.body.removeChild(textarea);
        }
    }
</script>
@endpush
@endsection