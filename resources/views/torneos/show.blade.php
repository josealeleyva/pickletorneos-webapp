@extends('layouts.dashboard')

@section('title', $torneo->nombre)
@section('page-title', $torneo->nombre)

@section('content')
<div class="max-w-6xl mx-auto space-y-4 sm:space-y-6">
    <!-- Banner del Torneo -->
    <div class="relative h-48 sm:h-64 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-lg overflow-hidden">
        @if($torneo->imagen_banner)
            <img src="{{ asset('storage/' . $torneo->imagen_banner) }}" alt="{{ $torneo->nombre }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
        @endif

        <div class="absolute bottom-0 left-0 right-0 p-4 sm:p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
                <div class="flex-1">
                    <h1 class="text-2xl sm:text-3xl md:text-4xl font-bold mb-2">{{ $torneo->nombre }}</h1>
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-sm">
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $torneo->deporte->nombre }}
                        </span>
                        <span class="flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $torneo->fecha_inicio->format('d/m/Y') }} - {{ $torneo->fecha_fin->format('d/m/Y') }}
                        </span>
                    </div>
                </div>

                @php
                    $estadoClasses = [
                        'borrador' => 'bg-gray-600',
                        'activo' => 'bg-green-600',
                        'en_curso' => 'bg-blue-600',
                        'finalizado' => 'bg-purple-600',
                        'cancelado' => 'bg-red-600',
                    ];
                    $estadoTextos = [
                        'borrador' => 'Borrador',
                        'activo' => 'Activo',
                        'en_curso' => 'En Curso',
                        'finalizado' => 'Finalizado',
                        'cancelado' => 'Cancelado',
                    ];
                @endphp
                <span class="px-4 py-2 text-sm font-semibold text-white rounded-lg {{ $estadoClasses[$torneo->estado] ?? 'bg-gray-600' }}">
                    {{ $estadoTextos[$torneo->estado] ?? 'Desconocido' }}
                </span>
            </div>
        </div>
    </div>

    {{-- ============================================================
         STATS CHIPS — solo mobile, integradas debajo del banner
    ============================================================ --}}
    @php
        $totalEquipos  = $torneo->equipos()->count();
        $totalPartidos = $torneo->partidos()->count();
        $totalGrupos   = $torneo->formato && $torneo->formato->tiene_grupos ? $torneo->grupos()->count() : null;
    @endphp
    <div class="flex flex-wrap gap-2 lg:hidden">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-100 text-blue-800 rounded-full text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
            {{ $totalEquipos }} equipos
        </span>
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-green-100 text-green-800 rounded-full text-sm font-semibold">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
            </svg>
            {{ $totalPartidos }} partidos
        </span>
        @if($totalGrupos !== null)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-purple-100 text-purple-800 rounded-full text-sm font-semibold">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                </svg>
                {{ $totalGrupos }} grupos
            </span>
        @endif
    </div>

    {{-- ============================================================
         BOTONES DE ACCIÓN (Publicar, Editar, Vista Pública, etc.)
    ============================================================ --}}
    <div class="flex flex-wrap gap-2 sm:gap-3">
        @if($torneo->estado === 'borrador')
            {{-- Borrador: solo editar datos básicos --}}
            <a href="{{ route('torneos.edit', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                Editar Torneo
            </a>

        @elseif($torneo->estado === 'activo')
            {{-- Activo: configuración completa + comenzar --}}
            @php
                $equiposCount  = $torneo->equipos()->count();
                $gruposCount   = $torneo->grupos()->count();
                $partidosCount = $torneo->partidos()->count();
                $partidosSinProgramar = $torneo->partidos()->where(function($q){ $q->whereNull('fecha_hora')->orWhereNull('cancha_id'); })->count();

                if ($torneo->formato && ($torneo->formato->esLiga() || $torneo->formato->esEliminacionDirecta())) {
                    $puedeComenzar = $equiposCount > 0 && $partidosCount > 0 && $partidosSinProgramar === 0;
                } else {
                    $puedeComenzar = $equiposCount > 0 && $gruposCount > 0 && $partidosCount > 0 && $partidosSinProgramar === 0;
                }
            @endphp

            @if($puedeComenzar)
                <form action="{{ route('torneos.comenzar', $torneo) }}" method="POST" class="inline"
                      onsubmit="return confirm('¿Estás seguro de comenzar el torneo? Una vez iniciado, no podrás modificar equipos ni el fixture.')">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Comenzar Torneo
                    </button>
                </form>
            @endif

            <a href="{{ route('torneos.public', $torneo->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Ver Vista Pública
            </a>

        @elseif($torneo->estado === 'en_curso')
            {{-- En curso: ver pública + finalizar (automático próximamente) --}}
            <a href="{{ route('torneos.public', $torneo->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Ver Vista Pública
            </a>

        @elseif($torneo->estado === 'finalizado')
            {{-- Finalizado: solo ver pública --}}
            <a href="{{ route('torneos.public', $torneo->id) }}" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                </svg>
                Ver Vista Pública
            </a>
        @endif

        @if($torneo->partidos()->where('estado', 'finalizado')->exists())
            <a href="{{ route('torneos.exportar-resultados', $torneo) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                </svg>
                Exportar Resultados
            </a>
        @endif

        <a href="{{ route('torneos.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg shadow-md transition text-sm">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver a Torneos
        </a>
    </div>

    {{-- ============================================================
         ACCIONES RÁPIDAS — solo mobile, debajo de los botones de estado
    ============================================================ --}}
    @php
        $esEliminacionDirecta = $torneo->formato && $torneo->formato->esEliminacionDirecta();
        if ($torneo->formato && ($torneo->formato->tiene_grupos || $esEliminacionDirecta)) {
            if ($esEliminacionDirecta) {
                $puedeVerLlavesMobile = $torneo->equipos()->count() > 0;
                $mensajeBloqMobile = 'Debes agregar equipos primero';
            } else {
                $partidosGrupoMobile = $torneo->partidos()->whereNotNull('grupo_id')->get();
                $puedeVerLlavesMobile = $partidosGrupoMobile->isNotEmpty() && $partidosGrupoMobile->every(fn($p) => $p->estado === 'finalizado');
                $mensajeBloqMobile = 'Debes finalizar todos los partidos de grupos primero';
            }
        } else {
            $puedeVerLlavesMobile = false;
            $mensajeBloqMobile = '';
        }
    @endphp

    <div class="lg:hidden bg-white rounded-lg shadow-sm p-4">
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Acciones</h3>
        <div class="space-y-2">
            <a href="{{ route('torneos.equipos.index', $torneo) }}"
               class="flex items-center gap-3 w-full px-4 py-3 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
                Gestionar Participantes
            </a>

            @if($torneo->estado === 'activo' && $torneo->formato && $torneo->formato->tiene_grupos)
                <a href="{{ route('torneos.grupos.index', $torneo) }}"
                   class="flex items-center gap-3 w-full px-4 py-3 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                    </svg>
                    Configurar Grupos
                </a>
            @endif

            @if($torneo->formato && ($torneo->formato->tiene_grupos || $torneo->formato->esLiga()))
                <a href="{{ route('torneos.fixture.index', $torneo) }}"
                   class="flex items-center gap-3 w-full px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition">
                    <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                    {{ $torneo->formato->tiene_grupos ? 'Ver Fixture de Grupos' : 'Gestionar Fixture' }}
                </a>
            @endif

            @if($torneo->formato && ($torneo->formato->tiene_grupos || $esEliminacionDirecta))
                @if($puedeVerLlavesMobile)
                    <a href="{{ route('torneos.llaves.index', $torneo) }}"
                       class="flex items-center gap-3 w-full px-4 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl transition">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z"></path>
                        </svg>
                        {{ $esEliminacionDirecta ? 'Gestionar Llaves' : 'Ver Llaves' }}
                    </a>
                @else
                    <button disabled title="{{ $mensajeBloqMobile }}"
                            class="flex items-center gap-3 w-full px-4 py-3 bg-gray-200 text-gray-400 font-semibold rounded-xl cursor-not-allowed">
                        <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z"></path>
                        </svg>
                        {{ $esEliminacionDirecta ? 'Gestionar Llaves' : 'Ver Llaves' }}
                    </button>
                @endif
            @endif
        </div>
    </div>

    {{-- ============================================================
         GRID PRINCIPAL
    ============================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Columna Principal -->
        <div class="lg:col-span-2 space-y-4 sm:space-y-6">

            <!-- Información General (colapsable en mobile) -->
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <button type="button" onclick="toggleSection('info-general-body', 'info-general-icon')"
                        class="w-full flex items-center justify-between p-4 sm:p-6 text-left">
                    <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Información General
                    </h2>
                    <svg id="info-general-icon" class="w-5 h-5 text-gray-400 transition-transform duration-200 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="info-general-body" class="collapsible-section px-4 pb-4 sm:px-6 sm:pb-6">
                    @if($torneo->descripcion)
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Descripción</h3>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $torneo->descripcion }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Deporte</h3>
                            <p class="text-sm text-gray-900 font-medium">{{ $torneo->deporte->nombre }}</p>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Categorías</h3>
                            <div class="flex flex-wrap gap-1.5 mt-1">
                                @forelse($torneo->categorias as $categoria)
                                    <span class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-indigo-100 text-indigo-800">
                                        {{ $categoria->nombre }}
                                    </span>
                                    @include('partials.categoria-restricciones', ['categoria' => $categoria])
                                @empty
                                    <p class="text-sm text-gray-500 italic">Sin categorías asignadas</p>
                                @endforelse
                            </div>
                        </div>

                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Complejo</h3>
                            <p class="text-sm text-gray-900 font-medium">{{ $torneo->complejo->nombre }}</p>
                            @if($torneo->complejo->direccion)
                                <p class="text-xs text-gray-500">{{ $torneo->complejo->direccion }}</p>
                            @endif
                        </div>

                        @if($torneo->fecha_limite_inscripcion)
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Límite Inscripción</h3>
                                <p class="text-sm text-gray-900 font-medium">{{ $torneo->fecha_limite_inscripcion->format('d/m/Y') }}</p>
                            </div>
                        @endif

                        @if($torneo->precio_inscripcion)
                            <div>
                                <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Precio Inscripción</h3>
                                <p class="text-sm text-gray-900 font-medium">${{ number_format($torneo->precio_inscripcion, 2) }}</p>
                            </div>
                        @endif

                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Organizador</h3>
                            <p class="text-sm text-gray-900 font-medium">{{ $torneo->organizador->name }} {{ $torneo->organizador->apellido }}</p>
                            @if($torneo->organizador->organizacion)
                                <p class="text-xs text-gray-500">{{ $torneo->organizador->organizacion }}</p>
                            @endif
                        </div>
                    </div>

                    @if($torneo->premios)
                        <div class="mt-4 pt-4 border-t border-gray-200">
                            <h3 class="text-sm font-semibold text-gray-700 mb-2">Premios</h3>
                            <p class="text-sm text-gray-600 whitespace-pre-line">{{ $torneo->premios }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reglamento (colapsable en mobile) -->
            @if($torneo->reglamento_texto || $torneo->reglamento_pdf)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <button type="button" onclick="toggleSection('reglamento-body', 'reglamento-icon')"
                            class="w-full flex items-center justify-between p-4 sm:p-6 text-left">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Reglamento
                        </h2>
                        <svg id="reglamento-icon" class="w-5 h-5 text-gray-400 transition-transform duration-200 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </button>

                    <div id="reglamento-body" class="collapsible-section px-4 pb-4 sm:px-6 sm:pb-6">
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

            <!-- Formato del Torneo (colapsable en mobile) -->
            @if($torneo->formato)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <button type="button" onclick="toggleSection('formato-body', 'formato-icon')"
                            class="w-full flex items-center justify-between p-4 sm:p-6 text-left">
                        <h2 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Formato del Torneo
                        </h2>
                        <svg id="formato-icon" class="w-5 h-5 text-gray-400 transition-transform duration-200 lg:hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>

                    <div id="formato-body" class="collapsible-section px-4 pb-4 sm:px-6 sm:pb-6 space-y-3">
                        <div>
                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Formato</h3>
                            <p class="text-sm text-gray-900 font-medium">{{ $torneo->formato->nombre }}</p>
                        </div>

                        @if($torneo->formato->tiene_grupos)
                            @if($torneo->tamanioGrupo)
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Número de Grupos</h3>
                                        <p class="text-sm text-gray-900 font-medium">{{ $torneo->numero_grupos }} grupos</p>
                                    </div>
                                    <div>
                                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Tamaño de Grupos</h3>
                                        <p class="text-sm text-gray-900 font-medium">{{ $torneo->tamanioGrupo->tamanio }} equipos</p>
                                    </div>
                                    @if($torneo->avanceGrupo)
                                        <div>
                                            <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Avance a Eliminación</h3>
                                            <p class="text-sm text-gray-900 font-medium">{{ $torneo->avanceGrupo->nombre }}</p>
                                        </div>
                                    @endif
                                </div>

                                @if($torneo->numero_grupos && $torneo->avanceGrupo)
                                    <div class="p-3 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg">
                                        <p class="text-xs text-green-800">
                                            <strong>Total de equipos:</strong> {{ $torneo->numero_grupos * $torneo->tamanioGrupo->tamanio }} equipos
                                            ({{ $torneo->numero_grupos }} grupos × {{ $torneo->tamanioGrupo->tamanio }} equipos)
                                            <br>
                                            <strong>Avanzan a eliminación:</strong>
                                            @php
                                                $directos = $torneo->avanceGrupo->cantidad_avanza_directo;
                                                $mejores  = $torneo->avanceGrupo->cantidad_avanza_mejores;
                                                $totalAvanzan = ($directos * $torneo->numero_grupos) + $mejores;
                                            @endphp
                                            {{ $totalAvanzan }} equipos
                                            @if($directos > 0 && $mejores > 0)
                                                ({{ $directos }} de cada grupo + {{ $mejores }} mejores segundos)
                                            @elseif($directos > 0)
                                                ({{ $directos }} de cada grupo)
                                            @endif
                                        </p>
                                    </div>
                                @endif
                            @endif
                        @else
                            @php
                                $esEliminacionDirectaFormato = $torneo->formato->esEliminacionDirecta();
                                $esLigaFormato = $torneo->formato->esLiga();
                            @endphp
                            <div class="p-3 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                                <p class="text-xs text-blue-900 mb-2">
                                    <strong>Sistema:</strong>
                                    @if($esEliminacionDirectaFormato)
                                        Llaves de eliminación directa por categoría
                                    @else
                                        Todos los equipos de cada categoría juegan entre sí
                                    @endif
                                </p>
                                <div class="space-y-1.5">
                                    @foreach($torneo->categorias as $categoria)
                                        @php
                                            $cuposCategoria    = $categoria->pivot->cupos_categoria ?? 0;
                                            $equiposCategoria  = $torneo->equipos()->where('categoria_id', $categoria->id)->count();

                                            if ($esLigaFormato) {
                                                $info = $equiposCategoria > 1 ? ($equiposCategoria * ($equiposCategoria - 1)) / 2 . ' partidos' : '';
                                            } else {
                                                $rondas = $equiposCategoria >= 2 ? ceil(log($equiposCategoria, 2)) : 0;
                                                $info   = $rondas > 0 ? $rondas . ($rondas == 1 ? ' ronda' : ' rondas') : '';
                                            }
                                        @endphp
                                        <div class="text-xs text-blue-800">
                                            <strong>{{ $categoria->nombre }}:</strong>
                                            @include('partials.categoria-restricciones', ['categoria' => $categoria])
                                            {{ $equiposCategoria }}/{{ $cuposCategoria }} equipos
                                            @if($info)({{ $info }})@endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-4 sm:space-y-6">
            <!-- Estadísticas — ocultas en mobile (ya están como chips arriba) -->
            <div class="hidden lg:block bg-white rounded-lg shadow-sm p-4 sm:p-6">
                <h2 class="text-lg font-bold text-gray-800 mb-4">Estadísticas</h2>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3 bg-blue-50 rounded-lg">
                        <span class="text-sm text-blue-900 font-medium">Equipos</span>
                        <span class="text-xl font-bold text-blue-600">{{ $totalEquipos }}</span>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-green-50 rounded-lg">
                        <span class="text-sm text-green-900 font-medium">Partidos</span>
                        <span class="text-xl font-bold text-green-600">{{ $totalPartidos }}</span>
                    </div>
                    @if($totalGrupos !== null)
                        <div class="flex items-center justify-between p-3 bg-purple-50 rounded-lg">
                            <span class="text-sm text-purple-900 font-medium">Grupos</span>
                            <span class="text-xl font-bold text-purple-600">{{ $totalGrupos }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Próximos Pasos — oculto en mobile -->
            @if($torneo->estado === 'activo')
                <div class="hidden lg:block bg-amber-50 border border-amber-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-amber-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                        Próximos Pasos
                    </h3>
                    <ul class="text-xs text-amber-800 space-y-2">
                        @if($torneo->formato && $torneo->formato->tiene_grupos)
                            <li class="flex items-start"><span class="mr-2">1.</span><span>Agregar equipos al torneo ({{ $torneo->equipos()->count() }} agregados)</span></li>
                            <li class="flex items-start"><span class="mr-2">2.</span><span>Configurar grupos</span></li>
                            <li class="flex items-start"><span class="mr-2">3.</span><span>Generar fixture de fase de grupos</span></li>
                            <li class="flex items-start"><span class="mr-2">4.</span><span>Programar fechas y horarios</span></li>
                            <li class="flex items-start"><span class="mr-2">5.</span><span>Comenzar el torneo</span></li>
                            <li class="flex items-start"><span class="mr-2">6.</span><span>Cargar resultados del Fixture</span></li>
                            <li class="flex items-start"><span class="mr-2">7.</span><span>Generar llaves de eliminación</span></li>
                            <li class="flex items-start"><span class="mr-2">8.</span><span>Cargar resultados — finalización automática</span></li>
                        @else
                            @php $esED = $torneo->formato->esEliminacionDirecta(); @endphp
                            @if($esED)
                                <li class="flex items-start"><span class="mr-2">1.</span><span>Agregar equipos al torneo ({{ $torneo->equipos()->count() }} agregados)</span></li>
                                <li class="flex items-start"><span class="mr-2">2.</span><span>Generar llaves de eliminación</span></li>
                                <li class="flex items-start"><span class="mr-2">3.</span><span>Comenzar el torneo</span></li>
                                <li class="flex items-start"><span class="mr-2">4.</span><span>Cargar resultados — finalización automática</span></li>
                            @else
                                <li class="flex items-start"><span class="mr-2">1.</span><span>Agregar equipos al torneo ({{ $torneo->equipos()->count() }} agregados)</span></li>
                                <li class="flex items-start"><span class="mr-2">2.</span><span>Generar fixture (todos contra todos)</span></li>
                                <li class="flex items-start"><span class="mr-2">3.</span><span>Programar fechas y horarios de partidos</span></li>
                                <li class="flex items-start"><span class="mr-2">4.</span><span>Comenzar el torneo</span></li>
                                <li class="flex items-start"><span class="mr-2">5.</span><span>Cargar resultados — finalización automática</span></li>
                            @endif
                        @endif
                    </ul>
                </div>
            @endif

            <!-- Acciones Rápidas — ocultas en mobile (ya están arriba) -->
            <div class="hidden lg:block bg-white rounded-lg shadow-sm p-4">
                <h3 class="text-sm font-semibold text-gray-800 mb-3">Acciones Rápidas</h3>
                <div class="space-y-2">
                    <a href="{{ route('torneos.equipos.index', $torneo) }}" class="w-full text-left px-3 py-2 text-sm bg-green-50 hover:bg-green-100 text-green-700 rounded-lg transition flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Gestionar Participantes
                    </a>

                    @if($torneo->estado === 'activo' && $torneo->formato && $torneo->formato->tiene_grupos)
                        <a href="{{ route('torneos.grupos.index', $torneo) }}" class="w-full text-left px-3 py-2 text-sm bg-purple-50 hover:bg-purple-100 text-purple-700 rounded-lg transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                            </svg>
                            Configurar Grupos
                        </a>
                    @endif

                    @if($torneo->formato && ($torneo->formato->tiene_grupos || $torneo->formato->esLiga()))
                        <a href="{{ route('torneos.fixture.index', $torneo) }}" class="w-full text-left px-3 py-2 text-sm bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                            </svg>
                            @if($torneo->formato->tiene_grupos) Ver Fixture de Grupos @else Gestionar Fixture @endif
                        </a>
                    @endif

                    @if($torneo->formato && ($torneo->formato->tiene_grupos || $torneo->formato->esEliminacionDirecta()))
                        @php
                            $esED2 = $torneo->formato->esEliminacionDirecta();
                            if ($esED2) {
                                $puedeVerLlaves2 = $torneo->equipos()->count() > 0;
                                $mensajeBloq2 = 'Debes agregar equipos primero';
                            } else {
                                $pg2 = $torneo->partidos()->whereNotNull('grupo_id')->get();
                                $puedeVerLlaves2 = $pg2->isNotEmpty() && $pg2->every(fn($p) => $p->estado === 'finalizado');
                                $mensajeBloq2 = 'Debes finalizar todos los partidos de la fase de grupos primero';
                            }
                        @endphp

                        @if($puedeVerLlaves2)
                            <a href="{{ route('torneos.llaves.index', $torneo) }}" class="w-full text-left px-3 py-2 text-sm bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg transition flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z"></path>
                                </svg>
                                @if($esED2) Gestionar Llaves @else Ver Llaves @endif
                            </a>
                        @else
                            <button disabled class="w-full text-left px-3 py-2 text-sm bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed flex items-center" title="{{ $mensajeBloq2 }}">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v7a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1v-2zM14 17a1 1 0 011-1h4a1 1 0 011 1v2a1 1 0 01-1 1h-4a1 1 0 01-1-1v-2z"></path>
                                </svg>
                                @if($esED2) Gestionar Llaves @else Ver Llaves @endif
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if(in_array($torneo->estado, ['activo', 'en_curso']))
    <div class="mt-8 pt-6 border-t border-gray-200">
        <details class="group">
            <summary class="cursor-pointer text-xs text-gray-400 hover:text-gray-500 select-none list-none flex items-center gap-1">
                <svg class="w-3 h-3 transition-transform group-open:rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
                Zona de peligro
            </summary>
            <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                <h3 class="text-sm font-semibold text-red-800 mb-1">Cancelar torneo</h3>
                <p class="text-xs text-red-600 mb-4">Una vez cancelado, el torneo no podrá reactivarse. El pago no es reembolsable.</p>
                <form action="{{ route('torneos.cancelar', $torneo) }}" method="POST"
                      onsubmit="return confirm('¿Estás seguro que deseas cancelar el torneo?\n\nEsta acción no se puede deshacer y el pago no será reembolsable.')">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-white border border-red-300 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                        Cancelar torneo
                    </button>
                </form>
            </div>
        </details>
    </div>
@endif

<script>
    // Colapsar secciones por defecto en mobile
    document.addEventListener('DOMContentLoaded', function () {
        if (window.innerWidth < 1024) {
            document.querySelectorAll('.collapsible-section').forEach(function (el) {
                el.style.display = 'none';
            });
        }
    });

    function toggleSection(contentId, iconId) {
        if (window.innerWidth >= 1024) return; // no colapsar en desktop
        var content = document.getElementById(contentId);
        var icon    = document.getElementById(iconId);
        var isHidden = content.style.display === 'none' || content.style.display === '';
        content.style.display = isHidden ? 'block' : 'none';
        if (icon) icon.style.transform = isHidden ? 'rotate(180deg)' : '';
    }
</script>
@endsection
