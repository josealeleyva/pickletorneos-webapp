<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $torneo->nombre }} - Punto de Oro</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    @auth
        @if(auth()->user()->hasRole('Jugador'))
        <!-- Barra de navegación del panel de jugador -->
        <div class="bg-indigo-900 text-white px-4 py-2 flex items-center justify-between">
            <a href="{{ route('jugador.torneos') }}" class="flex items-center gap-2 text-sm text-indigo-200 hover:text-white transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Volver a mis torneos
            </a>
            @if($misEquipoIds->isNotEmpty())
                <span class="text-xs bg-indigo-700 text-indigo-200 px-2 py-1 rounded-full">
                    Tu equipo está resaltado
                </span>
            @endif
        </div>
        @endif
    @endauth
    <!-- Hero Banner -->
    @if($torneo->imagen_banner)
        <div class="relative w-full h-48 sm:h-64 md:h-80 overflow-hidden">
            <img src="{{ asset('storage/' . $torneo->imagen_banner) }}"
                 alt="{{ $torneo->nombre }}"
                 class="w-full h-full object-cover">
            <!-- Overlay degradado para legibilidad del texto -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent"></div>
            <!-- Texto sobre el banner -->
            <div class="absolute bottom-0 left-0 right-0 px-4 sm:px-6 lg:px-8 pb-4 sm:pb-6">
                <div class="max-w-7xl mx-auto flex items-end justify-between gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold text-white drop-shadow">{{ $torneo->nombre }}</h1>
                        <p class="text-sm sm:text-base text-white/80 mt-1">{{ $torneo->deporte->nombre }} • {{ $torneo->complejo->nombre }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $torneo->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($torneo->estado) }}
                        </span>
                        <a href="{{ route('torneos.tv', $torneo->id) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-black/40 hover:bg-black/60 text-white border border-white/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Modo TV
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Sin banner: header con gradiente -->
        <header class="bg-gradient-to-r from-indigo-600 to-purple-700 shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
                <div class="flex items-start sm:items-center justify-between gap-3">
                    <div>
                        <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $torneo->nombre }}</h1>
                        <p class="text-sm sm:text-base text-indigo-100 mt-1">{{ $torneo->deporte->nombre }} • {{ $torneo->complejo->nombre }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $torneo->estado === 'activo' ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($torneo->estado) }}
                        </span>
                        <a href="{{ route('torneos.tv', $torneo->id) }}" target="_blank"
                           class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-sm font-semibold bg-white/10 hover:bg-white/20 text-white border border-white/20 transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                            Modo TV
                        </a>
                    </div>
                </div>
            </div>
        </header>
    @endif

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">
        <!-- Información General -->
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h2 class="text-lg font-semibold text-gray-800 mb-4">Información del Torneo</h2>

            @if($torneo->descripcion)
                <p class="text-gray-700 mb-4 whitespace-pre-line">{{ $torneo->descripcion }}</p>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div>
                    <span class="text-gray-500">Fecha Inicio:</span>
                    <span class="font-medium ml-2">{{ $torneo->fecha_inicio->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Fecha Fin:</span>
                    <span class="font-medium ml-2">{{ $torneo->fecha_fin->format('d/m/Y') }}</span>
                </div>
                <div>
                    <span class="text-gray-500">Formato:</span>
                    <span class="font-medium ml-2">{{ $torneo->formato->nombre }}</span>
                </div>
            </div>
        </div>

        {{-- Card de inscripción para jugadores --}}
        @if($torneo->estado === 'activo')
            @auth
                @if(auth()->user()->hasRole('Jugador') && auth()->user()->jugador)
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 sm:p-6 mb-6">
                    @if($yaInscripto)
                        <div class="flex items-center gap-2 text-green-700">
                            <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <div>
                                <p class="font-semibold text-sm">Ya estás inscripto en este torneo</p>
                                <a href="{{ route('jugador.inscripciones') }}" class="text-xs text-green-600 underline">Ver mis inscripciones</a>
                            </div>
                        </div>
                    @else
                        <h3 class="text-base font-semibold text-indigo-900 mb-1">¿Querés participar?</h3>
                        <p class="text-sm text-indigo-700 mb-3">Inscribite con tu equipo en este torneo.</p>
                        <a href="{{ route('torneos.inscripciones.crear', $torneo->id) }}"
                           class="inline-flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Inscribirse al torneo
                        </a>
                    @endif
                </div>
                @endif
            @else
                <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 sm:p-6 mb-6">
                    <p class="text-sm text-indigo-700 mb-3">
                        <a href="{{ route('login') }}" class="font-semibold underline">Iniciá sesión</a>
                        o
                        <a href="{{ route('register') }}" class="font-semibold underline">registrate</a>
                        para inscribirte en este torneo.
                    </p>
                </div>
            @endauth
        @endif

        <!-- Reglamento -->
        @if($torneo->reglamento_texto || $torneo->reglamento_pdf)
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <button type="button"
                onclick="toggleReglamento()"
                class="w-full flex items-center justify-between p-4 sm:p-6 text-left">
                <h2 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Reglamento
                </h2>
                <svg id="reglamento-pub-icon" class="w-5 h-5 text-gray-400 transition-transform duration-200 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>

            <div id="reglamento-pub-body" class="hidden px-4 pb-5 sm:px-6 sm:pb-6">
                @if($torneo->reglamento_pdf)
                    <a href="{{ asset('storage/' . $torneo->reglamento_pdf) }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-red-50 hover:bg-red-100 border border-red-200 text-red-700 font-semibold text-sm rounded-lg transition mb-4">
                        <svg class="w-5 h-5 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                        </svg>
                        Ver Reglamento (PDF)
                    </a>
                @endif
                @if($torneo->reglamento_texto)
                    <p class="text-sm text-gray-700 whitespace-pre-line leading-relaxed">{{ $torneo->reglamento_texto }}</p>
                @endif
            </div>
        </div>
        @endif

        <!-- Tabs de Categoría -->
        @if($torneo->categorias->count() > 1)
        <div class="bg-white rounded-lg shadow-sm">
            <!-- Desktop: Tabs horizontales -->
            <div class="hidden sm:block border-b border-gray-200">
                <nav class="flex overflow-x-auto" aria-label="Categorías">
                    <button type="button" data-categoria-filter="all" class="categoria-filter-tab whitespace-nowrap py-4 px-6 border-b-2 border-indigo-600 font-medium text-sm text-indigo-600">
                        Todas las Categorías
                    </button>
                    @foreach($torneo->categorias as $categoria)
                        <button type="button" data-categoria-filter="categoria-{{ $categoria->id }}" class="categoria-filter-tab whitespace-nowrap py-4 px-6 border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 font-medium text-sm">
                            <span class="block">{{ $categoria->nombre }}</span>
                            <span class="block mt-0.5">@include('partials.categoria-restricciones', ['categoria' => $categoria])</span>
                        </button>
                    @endforeach
                </nav>
            </div>

            <!-- Mobile: Selector dropdown -->
            <div class="sm:hidden p-4 border-b border-gray-200">
                <label class="block text-sm font-medium text-gray-700 mb-2">Filtrar por categoría:</label>
                <select id="categoria-filter-select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                    <option value="all">Todas las Categorías</option>
                    @foreach($torneo->categorias as $categoria)
                        @php
                            $restriccionTexto = collect([
                                $categoria->pivot->genero_permitido ? ucfirst($categoria->pivot->genero_permitido) : null,
                                ($categoria->pivot->edad_minima && $categoria->pivot->edad_maxima)
                                    ? $categoria->pivot->edad_minima.'-'.$categoria->pivot->edad_maxima.' años'
                                    : ($categoria->pivot->edad_minima
                                        ? '+'.$categoria->pivot->edad_minima.' años'
                                        : ($categoria->pivot->edad_maxima
                                            ? 'Hasta '.$categoria->pivot->edad_maxima.' años'
                                            : null)),
                            ])->filter()->implode(' · ');
                        @endphp
                        <option value="categoria-{{ $categoria->id }}">
                            {{ $categoria->nombre }}{{ $restriccionTexto ? ' ('.$restriccionTexto.')' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>
        @endif

        <!-- Buscador de partidos -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" id="buscar-partidos"
                       placeholder="Buscar por equipo o jugador..."
                       class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl text-base focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 bg-gray-50">
                <button id="limpiar-busqueda" class="hidden absolute inset-y-0 right-0 pr-3 flex items-center">
                    <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <p id="busqueda-sin-resultados" class="hidden text-sm text-gray-500 mt-2 text-center">No se encontraron partidos con ese nombre.</p>
        </div>

        <!-- Tabs de Vistas -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="border-b border-gray-200">
                <nav class="flex -mb-px overflow-x-auto" aria-label="Tabs">
                    @php
                        $esLiga = $torneo->formato && $torneo->formato->esLiga();
                        $esEliminacionDirecta = $torneo->formato && $torneo->formato->esEliminacionDirecta();
                    @endphp
                    @if($esEliminacionDirecta)
                    <button onclick="switchTab('grupos')" id="tab-grupos" class="tab-button active px-6 py-3 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">
                        Equipos
                    </button>
                    @if(!empty($llavesPorCategoria))
                        <button onclick="switchTab('llaves')" id="tab-llaves" class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Llaves
                        </button>
                    @endif
                    @else
                    <button onclick="switchTab('grupos')" id="tab-grupos" class="tab-button active px-6 py-3 text-sm font-medium border-b-2 border-indigo-600 text-indigo-600">
                        {{ $esLiga ? 'Equipos' : 'Grupos' }}
                    </button>
                    <button onclick="switchTab('fixture')" id="tab-fixture" class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Fixture
                    </button>
                    <button onclick="switchTab('posiciones')" id="tab-posiciones" class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        Posiciones
                    </button>
                    @if(!empty($llavesPorCategoria))
                        <button onclick="switchTab('llaves')" id="tab-llaves" class="tab-button px-6 py-3 text-sm font-medium border-b-2 border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300">
                            Llaves
                        </button>
                    @endif
                    @endif
                </nav>
            </div>

            <!-- Vista de Grupos/Equipos -->
            <div id="content-grupos" class="tab-content p-6">
                @if($esLiga || $esEliminacionDirecta)
                    {{-- Liga o Eliminación Directa: Mostrar equipos por categoría --}}
                    @foreach($torneo->categorias as $categoria)
                        @php
                            $equiposCategoria = $torneo->equipos->where('categoria_id', $categoria->id)->sortBy('nombre');
                        @endphp
                        @if($equiposCategoria->isNotEmpty())
                            <div class="grupo-section mb-6 last:mb-0" data-categoria="categoria-{{ $categoria->id }}">
                                <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg">{{ $categoria->nombre }}</span>
                                    @include('partials.categoria-restricciones', ['categoria' => $categoria])
                                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">{{ $equiposCategoria->count() }} equipos</span>
                                </h3>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                    @foreach($equiposCategoria as $equipo)
                                        <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                            <div class="font-medium text-gray-900">{{ $equipo->nombre }}</div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endforeach

                    @if($torneo->equipos->isEmpty())
                        <p class="text-gray-500 text-center py-8">No hay equipos inscritos aún.</p>
                    @endif
                @else
                    {{-- Fase de Grupos: Mostrar grupos tradicionales --}}
                    @forelse($torneo->grupos->sortBy('orden') as $grupo)
                        <div class="grupo-section mb-6 last:mb-0" data-categoria="categoria-{{ $grupo->categoria_id }}">
                            <h3 class="text-lg font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg">{{ $grupo->nombre }}</span>
                                @if($grupo->categoria)
                                    <span class="text-xs bg-purple-100 text-purple-700 px-2 py-1 rounded">{{ $grupo->categoria->nombre }}</span>
                                @endif
                            </h3>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3 mt-3">
                                @foreach($grupo->equipos->sortBy('nombre') as $equipo)
                                    <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                        <div class="font-medium text-gray-900">{{ $equipo->nombre }}</div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <p class="text-gray-500 text-center py-8">No hay grupos configurados aún.</p>
                    @endforelse
                @endif
            </div>

            <!-- Vista de Fixture -->
            <div id="content-fixture" class="tab-content hidden p-6">
                @if($partidosPorFecha->isNotEmpty())
                    @foreach($partidosPorFecha as $fecha => $partidos)
                        <div class="mb-6 last:mb-0">
                            <h3 class="text-lg font-bold text-gray-800 mb-3">
                                {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd D [de] MMMM, YYYY') }}
                            </h3>

                            <div class="space-y-3">
                                @foreach($partidos->sortBy('fecha_hora') as $partido)
                                    @php
                                        // Determinar categoría del partido para filtrado
                                        $partidoCategoria = null;
                                        if ($partido->grupo) {
                                            $partidoCategoria = $partido->grupo->categoria_id;
                                        } elseif ($partido->equipo1 && $partido->equipo1->categoria) {
                                            $partidoCategoria = $partido->equipo1->categoria_id;
                                        }
                                        $equiposSearch = strtolower(($partido->equipo1 ? $partido->equipo1->nombre : '') . ' ' . ($partido->equipo2 ? $partido->equipo2->nombre : ''));
                                        $esMiPartido = isset($misEquipoIds) && $misEquipoIds->isNotEmpty() &&
                                            ($misEquipoIds->contains($partido->equipo1_id) || $misEquipoIds->contains($partido->equipo2_id));
                                    @endphp
                                    <div class="{{ $esMiPartido ? 'bg-indigo-50 border-indigo-300 ring-1 ring-indigo-300' : 'bg-white border-gray-200' }} rounded-xl overflow-hidden hover:shadow-md transition partido-item"
                                         data-categoria="categoria-{{ $partidoCategoria }}"
                                         data-equipos="{{ $equiposSearch }}">
                                        @if($esMiPartido)
                                            <div class="bg-indigo-600 text-white text-xs font-semibold px-3 py-1 flex items-center gap-1">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/></svg>
                                                Tu partido
                                            </div>
                                        @endif

                                        <!-- Fecha/hora/cancha prominente -->
                                        @if($partido->fecha_hora || $partido->cancha)
                                        <div class="bg-indigo-50 px-4 py-2.5 border-b border-indigo-100 flex flex-wrap items-center gap-x-4 gap-y-1">
                                            @if($partido->fecha_hora)
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-base font-bold text-indigo-700">{{ $partido->fecha_hora->format('H:i') }}</span>
                                            </div>
                                            @endif
                                            @if($partido->cancha)
                                            <div class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                </svg>
                                                <span class="text-sm font-semibold text-gray-800">{{ $partido->cancha->nombre }}</span>
                                                <span class="text-xs text-gray-500">· {{ $partido->cancha->complejo->nombre }}</span>
                                            </div>
                                            @endif
                                            @if($partido->grupo)
                                                <span class="ml-auto text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-medium">{{ $partido->grupo->nombre }}</span>
                                            @endif
                                        </div>
                                        @endif

                                        <!-- Enfrentamiento -->
                                        <div class="px-4 py-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="text-sm font-semibold text-gray-900 flex-1 min-w-0 truncate">
                                                    {{ $partido->equipo1 ? $partido->equipo1->nombre : '—' }}
                                                </div>
                                                @if($partido->estado === 'finalizado' && $partido->juegos->isNotEmpty())
                                                    <div class="flex gap-1 flex-shrink-0">
                                                        @foreach($partido->juegos->sortBy('numero_juego') as $juego)
                                                            <span class="bg-green-100 text-green-700 px-2 py-0.5 rounded font-bold text-xs">
                                                                {{ $juego->juegos_equipo1 }}-{{ $juego->juegos_equipo2 }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @else
                                                    <div class="text-gray-400 font-bold text-sm flex-shrink-0">vs</div>
                                                @endif
                                                <div class="text-sm font-semibold text-gray-900 flex-1 min-w-0 truncate text-right">
                                                    {{ $partido->equipo2 ? $partido->equipo2->nombre : '—' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-center py-8">No hay partidos programados aún.</p>
                @endif
            </div>

            <!-- Vista de Posiciones -->
            <div id="content-posiciones" class="tab-content hidden p-6">
                @if(!empty($tablaPosiciones))
                    @php
                        $posicionesPorCategoria = collect($tablaPosiciones)->groupBy('categoria_id');
                    @endphp

                    @foreach($posicionesPorCategoria as $categoriaId => $gruposCategoria)
                        <div class="posicion-categoria mb-8 last:mb-0" data-categoria="categoria-{{ $categoriaId }}">
                            <!-- Título de categoría -->
                            @php
                                $primerGrupo = $gruposCategoria->first();
                                $categoriaNombre = $primerGrupo['categoria_nombre'];
                                $campeonId = $primerGrupo['campeon_id'] ?? null;
                            @endphp
                            <div class="mb-4 pb-2 border-b-2 border-indigo-200">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <h2 class="text-xl font-bold text-indigo-700">Categoría {{ $categoriaNombre }}</h2>

                                    {{-- Mostrar campeón si existe (Liga) --}}
                                    @if($esLiga && $campeonId)
                                        @php
                                            $equipoCampeon = \App\Models\Equipo::find($campeonId);
                                        @endphp
                                        @if($equipoCampeon)
                                            <div class="flex items-center gap-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 px-4 py-2 rounded-lg shadow-lg">
                                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                                <div>
                                                    <div class="text-xs font-semibold uppercase">Campeón</div>
                                                    <div class="font-bold">{{ $equipoCampeon->nombre }}</div>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Tablas de cada grupo de esta categoría -->
                            @foreach($gruposCategoria as $grupoData)
                                <div class="mb-6 last:mb-0">
                                    @if(!$esLiga)
                                        <h3 class="text-lg font-bold text-gray-800 mb-4">
                                            <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg inline-block">{{ $grupoData['grupo_nombre'] }}</span>
                                        </h3>
                                    @endif

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 border border-gray-200 rounded-lg">
                                            <thead class="bg-gray-100">
                                                <tr>
                                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase">#</th>
                                                    <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Equipo</th>
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">PJ</th>
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">PG</th>
                                                    @if($torneo->deporte->permiteEmpates())
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">PE</th>
                                                    @endif
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">PP</th>
                                                    @if($torneo->deporte->usaSets())
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">SG</th>
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">SP</th>
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">DifS</th>
                                                    @endif
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">{{ $torneo->deporte->usaSets() ? 'GF' : 'GF' }}</th>
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">{{ $torneo->deporte->usaSets() ? 'GC' : 'GC' }}</th>
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">{{ $torneo->deporte->usaSets() ? 'DifG' : 'DifG' }}</th>
                                                    <th class="px-3 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Pts</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($grupoData['posiciones'] as $index => $pos)
                                                    @php
                                                        $cantidadClasifican = $grupoData['cantidad_clasifican'] ?? 0;
                                                        $clasificaDirecto = $cantidadClasifican > 0 && $index < $cantidadClasifican;
                                                        $esCampeon = $campeonId && $pos['equipo']->id === $campeonId;
                                                    @endphp
                                                    <tr class="hover:bg-gray-50 {{ $clasificaDirecto ? 'bg-green-50' : '' }} {{ $esCampeon ? 'bg-yellow-50 border-l-4 border-yellow-500' : '' }}">
                                                        <td class="px-3 py-3 text-sm font-bold text-gray-900">
                                                            {{ $index + 1 }}
                                                            @if($clasificaDirecto)
                                                                <span class="ml-1 text-green-600" title="Clasifica">✓</span>
                                                            @endif
                                                            @if($esCampeon)
                                                                <span class="ml-1 text-yellow-600" title="Campeón">🏆</span>
                                                            @endif
                                                        </td>
                                                        <td class="px-3 py-3">
                                                            <div class="text-sm font-semibold text-gray-900 {{ $esCampeon ? 'font-bold' : '' }}">{{ $pos['equipo']->nombre }}</div>
                                                        </td>
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['pj'] }}</td>
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['pg'] }}</td>
                                                        @if($torneo->deporte->permiteEmpates())
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['pe'] ?? 0 }}</td>
                                                        @endif
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['pp'] }}</td>
                                                        @if($torneo->deporte->usaSets())
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['sg'] }}</td>
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['sp'] }}</td>
                                                        <td class="px-3 py-3 text-sm text-center">
                                                            <span class="font-semibold {{ $pos['diferencia_sets'] > 0 ? 'text-green-600' : ($pos['diferencia_sets'] < 0 ? 'text-red-600' : 'text-gray-900') }}">
                                                                {{ $pos['diferencia_sets'] > 0 ? '+' : '' }}{{ $pos['diferencia_sets'] }}
                                                            </span>
                                                        </td>
                                                        @endif
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['pf'] }}</td>
                                                        <td class="px-3 py-3 text-sm text-gray-900 text-center">{{ $pos['pc'] }}</td>
                                                        <td class="px-3 py-3 text-sm text-center">
                                                            <span class="font-semibold {{ $pos['diferencia_puntos'] > 0 ? 'text-green-600' : ($pos['diferencia_puntos'] < 0 ? 'text-red-600' : 'text-gray-900') }}">
                                                                {{ $pos['diferencia_puntos'] > 0 ? '+' : '' }}{{ $pos['diferencia_puntos'] }}
                                                            </span>
                                                        </td>
                                                        <td class="px-3 py-3 text-sm font-bold text-indigo-600 text-center">{{ $pos['puntos'] }}</td>
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
                    <p class="text-gray-500 text-center py-8">Aún no hay resultados cargados para calcular posiciones.</p>
                @endif
            </div>

            <!-- Vista de Llaves -->
            @if(!empty($llavesPorCategoria))
                <div id="content-llaves" class="tab-content hidden p-6">
                    @foreach($llavesPorCategoria as $catId => $data)
                        <div class="llaves-categoria mb-8 last:mb-0" data-categoria="categoria-{{ $catId }}">
                            <!-- Título de categoría con campeón -->
                            <div class="mb-6 pb-2 border-b-2 border-indigo-200">
                                <div class="flex items-center justify-between flex-wrap gap-3">
                                    <h2 class="text-xl font-bold text-indigo-700">{{ $data['categoria']->nombre }}</h2>

                                    @php
                                        // Buscar la llave de final y verificar si tiene ganador
                                        $llavesFinalPublic = $data['llaves_por_ronda']['Final'] ?? collect();
                                        $llaveFinalPublic = $llavesFinalPublic->first();
                                        $campeonPublic = null;
                                        if ($llaveFinalPublic && $llaveFinalPublic->partido && $llaveFinalPublic->partido->estado === 'finalizado' && $llaveFinalPublic->partido->equipoGanador) {
                                            $campeonPublic = $llaveFinalPublic->partido->equipoGanador;
                                        }

                                        /**
                                         * Función helper para determinar si una llave vacía es BYE o "Esperando Llave #X"
                                         */
                                        $determinarEstadoEquipoPublic = function($llave, $posicion) use ($data) {
                                            if ($llave->{$posicion}) {
                                                return ['mensaje' => $llave->{$posicion}->nombre];
                                            }

                                            // Buscar llaves que avanzan a esta llave
                                            $llavesQueAvanzanAqui = collect();
                                            foreach ($data['llaves_por_ronda'] as $ronda => $llaves) {
                                                foreach ($llaves as $llaveAnterior) {
                                                    if ($llaveAnterior->proxima_llave_id == $llave->id) {
                                                        $llavesQueAvanzanAqui->push($llaveAnterior);
                                                    }
                                                }
                                            }

                                            if ($llavesQueAvanzanAqui->isEmpty()) {
                                                return ['mensaje' => 'BYE'];
                                            }

                                            $llavesQueAvanzanAqui = $llavesQueAvanzanAqui->sortBy('orden')->values();
                                            $llaveAnteriorIndex = ($posicion === 'equipo1') ? 0 : 1;
                                            $llaveAnterior = $llavesQueAvanzanAqui->get($llaveAnteriorIndex);

                                            if (!$llaveAnterior) {
                                                return ['mensaje' => 'BYE'];
                                            }

                                            if (!$llaveAnterior->equipo1_id && !$llaveAnterior->equipo2_id) {
                                                // Verificar recursivamente
                                                $llavesQueLlenanLaAnterior = collect();
                                                foreach ($data['llaves_por_ronda'] as $r => $lls) {
                                                    foreach ($lls as $ll) {
                                                        if ($ll->proxima_llave_id == $llaveAnterior->id) {
                                                            $llavesQueLlenanLaAnterior->push($ll);
                                                        }
                                                    }
                                                }

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

                                            if (($llaveAnterior->equipo1_id && !$llaveAnterior->equipo2_id) ||
                                                (!$llaveAnterior->equipo1_id && $llaveAnterior->equipo2_id)) {
                                                return ['mensaje' => 'BYE'];
                                            }

                                            return ['mensaje' => 'Esperando Llave #' . $llaveAnterior->orden];
                                        };
                                    @endphp

                                    @if($campeonPublic)
                                        <div class="flex items-center gap-3 bg-gradient-to-r from-yellow-400 to-yellow-500 text-gray-900 px-4 py-2 rounded-lg shadow-lg">
                                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                            <div>
                                                <div class="text-xs font-semibold uppercase">Campeón</div>
                                                <div class="font-bold">{{ $campeonPublic->nombre }}</div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($data['llaves_por_ronda']->isEmpty())
                                <p class="text-gray-500 text-center py-4">No hay llaves generadas para esta categoría.</p>
                            @else
                                <!-- Rondas -->
                                <div class="space-y-6">
                                    @foreach($data['rondas'] as $rondaNombre)
                                        @php
                                            $llaves = $data['llaves_por_ronda'][$rondaNombre] ?? collect();
                                        @endphp

                                        <div class="ronda-section">
                                            <h3 class="text-lg font-bold text-gray-800 mb-4">
                                                <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-lg inline-block">
                                                    {{ $rondaNombre }}
                                                </span>
                                            </h3>

                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                @foreach($llaves as $llave)
                                                    @php
                                                        $llaveBusqueda = strtolower(($llave->equipo1 ? $llave->equipo1->nombre : '') . ' ' . ($llave->equipo2 ? $llave->equipo2->nombre : ''));
                                                    @endphp
                                                    <div class="bg-white border-2 border-gray-200 rounded-xl overflow-hidden llave-item" data-equipos="{{ $llaveBusqueda }}">
                                                        <!-- Header -->
                                                        <div class="bg-indigo-50 px-4 py-3 border-b border-indigo-100">
                                                            <div class="flex items-center justify-between mb-1">
                                                                <span class="text-xs font-semibold text-indigo-500 uppercase tracking-wide">Llave #{{ $llave->orden }}</span>
                                                            </div>
                                                            @if($llave->partido)
                                                                @if($llave->partido->fecha_hora)
                                                                    <div class="flex items-center gap-1.5 mt-1">
                                                                        <svg class="w-4 h-4 text-indigo-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                                        </svg>
                                                                        <span class="text-sm font-bold text-gray-900">{{ $llave->partido->fecha_hora->format('d/m/Y') }}</span>
                                                                        <span class="text-base font-bold text-indigo-700">{{ $llave->partido->fecha_hora->format('H:i') }}</span>
                                                                    </div>
                                                                @endif

                                                                @if($llave->partido->cancha)
                                                                    <div class="flex items-center gap-1.5 mt-1">
                                                                        <svg class="w-4 h-4 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                                        </svg>
                                                                        <span class="text-sm font-semibold text-gray-800">{{ $llave->partido->cancha->nombre }}</span>
                                                                        <span class="text-xs text-gray-500">· {{ $llave->partido->cancha->complejo->nombre }}</span>
                                                                    </div>
                                                                @endif
                                                            @endif
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
                                                                    <div class="px-2 py-2 mt-1 {{ $llave->partido->equipo_ganador_id === $llave->equipo1_id ? 'bg-green-100' : '' }}">
                                                                        <span class="text-sm font-semibold {{ $llave->partido->equipo_ganador_id === $llave->equipo1_id ? 'text-green-900' : 'text-gray-400' }} italic">
                                                                            {{ $llave->equipo1 ? $llave->equipo1->nombre : $determinarEstadoEquipoPublic($llave, 'equipo1')['mensaje'] }}
                                                                        </span>
                                                                    </div>
                                                                    <div class="px-2 py-2 {{ $llave->partido->equipo_ganador_id === $llave->equipo2_id ? 'bg-green-100' : '' }}">
                                                                        <span class="text-sm font-semibold {{ $llave->partido->equipo_ganador_id === $llave->equipo2_id ? 'text-green-900' : 'text-gray-400' }} italic">
                                                                            {{ $llave->equipo2 ? $llave->equipo2->nombre : $determinarEstadoEquipoPublic($llave, 'equipo2')['mensaje'] }}
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
                                                                            <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipoPublic($llave, 'equipo1')['mensaje'] }}</span>
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
                                                                            <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipoPublic($llave, 'equipo2')['mensaje'] }}</span>
                                                                        @endif
                                                                    </div>
                                                                @endif
                                                            @else
                                                                <!-- Vista normal sin resultados -->
                                                                <!-- Equipo 1 -->
                                                                <div class="px-3 py-3">
                                                                    @if($llave->equipo1)
                                                                        <div class="text-sm text-gray-800 font-medium">{{ $llave->equipo1->nombre }}</div>
                                                                    @else
                                                                        <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipoPublic($llave, 'equipo1')['mensaje'] }}</span>
                                                                    @endif
                                                                </div>

                                                                <!-- Equipo 2 -->
                                                                <div class="px-3 py-3">
                                                                    @if($llave->equipo2)
                                                                        <div class="text-sm text-gray-800 font-medium">{{ $llave->equipo2->nombre }}</div>
                                                                    @else
                                                                        <span class="text-sm text-gray-400 italic">{{ $determinarEstadoEquipoPublic($llave, 'equipo2')['mensaje'] }}</span>
                                                                    @endif
                                                                </div>
                                                            @endif
                                                        </div>

                                                        <!-- Footer -->
                                                        <div class="bg-gray-50 px-3 py-2 border-t border-gray-200 flex items-center justify-between">
                                                            @if(!$llave->partido)
                                                                <span class="text-xs text-gray-500">Esperando equipos</span>
                                                            @elseif($llave->partido->estado === 'programado')
                                                                <span class="text-xs text-blue-600">Programado</span>
                                                                @if($llave->partido->cancha)
                                                                    <span class="text-xs text-gray-500">{{ $llave->partido->cancha->nombre }}</span>
                                                                @endif
                                                            @elseif($llave->partido->estado === 'finalizado')
                                                                <span class="text-xs text-green-600 font-semibold">Finalizado</span>
                                                                @if($llave->partido->cancha)
                                                                    <span class="text-xs text-gray-500">{{ $llave->partido->cancha->nombre }}</span>
                                                                @endif
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </main>

    <footer class="bg-white border-t border-gray-200 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <p class="text-sm text-gray-500 text-center">
                Organizado por <strong>{{ $torneo->organizador->name }}</strong> • Complejo {{ $torneo->complejo->nombre }}
            </p>
        </div>
    </footer>

    <script>
    // Variables para filtrado por categoría
    const grupoSections = document.querySelectorAll('.grupo-section');
    const posicionCategorias = document.querySelectorAll('.posicion-categoria');
    const llavesCategorias = document.querySelectorAll('.llaves-categoria');
    const partidoItems = document.querySelectorAll('.partido-item');
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

        // Filtrar secciones de llaves
        llavesCategorias.forEach(section => {
            if (categoriaId === 'all' || section.dataset.categoria === categoriaId) {
                section.classList.remove('hidden');
            } else {
                section.classList.add('hidden');
            }
        });

        // Filtrar partidos individuales
        partidoItems.forEach(item => {
            if (categoriaId === 'all' || item.dataset.categoria === categoriaId) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });

        // Actualizar tabs (desktop)
        categoriaFilterTabs.forEach(tab => {
            if (tab.dataset.categoriaFilter === categoriaId) {
                tab.classList.remove('border-transparent', 'text-gray-500');
                tab.classList.add('border-indigo-600', 'text-indigo-600');
            } else {
                tab.classList.remove('border-indigo-600', 'text-indigo-600');
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
        document.querySelectorAll('.tab-button').forEach(btn => {
            btn.classList.remove('active', 'border-indigo-600', 'text-indigo-600');
            btn.classList.add('border-transparent', 'text-gray-500');
        });

        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });

        document.getElementById('tab-' + tab).classList.add('active', 'border-indigo-600', 'text-indigo-600');
        document.getElementById('tab-' + tab).classList.remove('border-transparent', 'text-gray-500');
        document.getElementById('content-' + tab).classList.remove('hidden');

        // Aplicar filtro de categoría actual al cambiar de tab
        filtrarPorCategoria(categoriaActual);

        // Re-aplicar búsqueda al cambiar de tab
        const inputBusqueda = document.getElementById('buscar-partidos');
        if (inputBusqueda && inputBusqueda.value.trim()) {
            aplicarBusqueda(inputBusqueda.value.trim());
        }
    }

    // ===== BUSCADOR DE PARTIDOS =====
    const inputBusqueda = document.getElementById('buscar-partidos');
    const btnLimpiar = document.getElementById('limpiar-busqueda');
    const msgSinResultados = document.getElementById('busqueda-sin-resultados');
    let textoBusqueda = '';

    function aplicarBusqueda(texto) {
        textoBusqueda = texto.toLowerCase().trim();
        const hayBusqueda = textoBusqueda.length > 0;

        // Mostrar/ocultar botón limpiar
        if (btnLimpiar) {
            btnLimpiar.classList.toggle('hidden', !hayBusqueda);
        }

        // Filtrar partido-items (fixture)
        const todosPartidos = document.querySelectorAll('.partido-item');
        todosPartidos.forEach(item => {
            const equipos = (item.dataset.equipos || '').toLowerCase();
            const categoria = item.dataset.categoria;
            const matchBusqueda = !hayBusqueda || equipos.includes(textoBusqueda);
            const matchCategoria = categoriaActual === 'all' || categoria === categoriaActual;

            item.classList.toggle('hidden', !(matchBusqueda && matchCategoria));
        });

        // Filtrar llave-items (llaves)
        const todasLlaves = document.querySelectorAll('.llave-item');
        todasLlaves.forEach(item => {
            const equipos = (item.dataset.equipos || '').toLowerCase();
            const matchBusqueda = !hayBusqueda || equipos.includes(textoBusqueda);
            item.classList.toggle('hidden', !matchBusqueda);
        });

        // Ocultar rondas que quedaron sin llaves visibles
        document.querySelectorAll('.ronda-section').forEach(ronda => {
            const tieneVisibles = ronda.querySelectorAll('.llave-item:not(.hidden)').length > 0;
            ronda.classList.toggle('hidden', !tieneVisibles);
        });

        // Ocultar categorías de llaves que quedaron sin rondas visibles
        document.querySelectorAll('.llaves-categoria').forEach(cat => {
            const tieneVisibles = cat.querySelectorAll('.ronda-section:not(.hidden)').length > 0;
            cat.classList.toggle('hidden', !tieneVisibles);
        });

        // Mostrar mensaje si no hay resultados visibles
        let hayResultados = false;
        document.querySelectorAll('.tab-content:not(.hidden) .partido-item:not(.hidden), .tab-content:not(.hidden) .llave-item:not(.hidden)').forEach(() => {
            hayResultados = true;
        });

        if (msgSinResultados) {
            msgSinResultados.classList.toggle('hidden', !hayBusqueda || hayResultados);
        }
    }

    if (inputBusqueda) {
        inputBusqueda.addEventListener('input', function() {
            aplicarBusqueda(this.value);
        });
    }

    if (btnLimpiar) {
        btnLimpiar.addEventListener('click', function() {
            inputBusqueda.value = '';
            aplicarBusqueda('');
            inputBusqueda.focus();
        });
    }

    // Override filtrarPorCategoria para que también respete la búsqueda activa
    const _filtrarPorCategoria = filtrarPorCategoria;
    filtrarPorCategoria = function(categoriaId) {
        _filtrarPorCategoria(categoriaId);
        if (textoBusqueda) {
            aplicarBusqueda(textoBusqueda);
        }
    };

    function toggleReglamento() {
        var body = document.getElementById('reglamento-pub-body');
        var icon = document.getElementById('reglamento-pub-icon');
        var hidden = body.classList.toggle('hidden');
        icon.style.transform = hidden ? '' : 'rotate(180deg)';
    }
    </script>
</body>
</html>
