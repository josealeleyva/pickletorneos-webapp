@extends('layouts.dashboard')

@section('title', 'Mis Torneos')
@section('page-title', 'Mis Torneos')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header con botón de crear -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-gray-600 text-sm sm:text-base">Gestiona todos tus torneos desde un solo lugar</p>
        </div>
        <a href="{{ route('torneos.create') }}" class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 w-full sm:w-auto justify-center text-sm sm:text-base">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nuevo Torneo
        </a>
    </div>

    @if($torneos->count() > 0)
        <!-- Grid de torneos -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @foreach($torneos as $torneo)
                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200 overflow-hidden">
                    <!-- Banner o placeholder -->
                    <div class="h-32 sm:h-40 bg-gradient-to-r from-indigo-500 to-purple-600 relative">
                        @if($torneo->imagen_banner)
                            <img src="{{ asset('storage/' . $torneo->imagen_banner) }}" alt="{{ $torneo->nombre }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                <svg class="w-16 h-16 text-white opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        @endif

                        <!-- Badge de estado -->
                        <div class="absolute top-2 right-2">
                            @php
                                $estadoClasses = [
                                    'borrador' => 'bg-gray-500',
                                    'activo' => 'bg-green-500',
                                    'en_curso' => 'bg-blue-500',
                                    'finalizado' => 'bg-purple-500',
                                    'cancelado' => 'bg-red-500',
                                ];
                                $estadoTextos = [
                                    'borrador' => 'Borrador',
                                    'activo' => 'Activo',
                                    'en_curso' => 'En Curso',
                                    'finalizado' => 'Finalizado',
                                    'cancelado' => 'Cancelado',
                                ];
                            @endphp
                            <span class="px-2 py-1 text-xs font-semibold text-white rounded {{ $estadoClasses[$torneo->estado] ?? 'bg-gray-500' }}">
                                {{ $estadoTextos[$torneo->estado] ?? 'Desconocido' }}
                            </span>
                        </div>
                    </div>

                    <!-- Contenido -->
                    <div class="p-4 sm:p-5">
                        <h3 class="text-lg sm:text-xl font-bold text-gray-900 mb-2 truncate">{{ $torneo->nombre }}</h3>

                        <div class="space-y-2 text-sm">
                            <!-- Deporte -->
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span>{{ $torneo->deporte->nombre }}</span>
                            </div>

                            <!-- Complejo -->
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <span class="truncate">{{ $torneo->complejo->nombre }}</span>
                            </div>

                            <!-- Fechas -->
                            <div class="flex items-center text-gray-600">
                                <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span>{{ $torneo->fecha_inicio->format('d/m/Y') }} - {{ $torneo->fecha_fin->format('d/m/Y') }}</span>
                            </div>

                            <!-- Formato -->
                            @if($torneo->formato)
                                <div class="flex items-center text-gray-600">
                                    <svg class="w-4 h-4 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                    <span class="truncate">{{ $torneo->formato->nombre }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Estado de pago (si está pendiente) -->
                    @if($torneo->pago && $torneo->pago->estado === 'pendiente')
                        <div class="px-4 sm:px-5 py-3 bg-yellow-50 border-t border-yellow-200">
                            <div class="flex items-center gap-2 mb-2">
                                <svg class="w-5 h-5 text-yellow-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm font-semibold text-yellow-800">Pago Pendiente</span>
                            </div>
                            <p class="text-xs text-yellow-700 mb-3">
                                Debes completar el pago de ${{ number_format($torneo->pago->monto, 0, ',', '.') }} para acceder al torneo.
                            </p>
                        </div>
                    @endif

                    <!-- Footer con acciones -->
                    <div class="px-4 sm:px-5 py-3 bg-gray-50 border-t border-gray-200 flex flex-wrap gap-2 justify-between items-center">
                        @if($torneo->pago && $torneo->pago->estado === 'pendiente')
                            <!-- Si el pago está pendiente, solo mostrar botón de pagar -->
                            <a href="{{ route('pagos.checkout', $torneo) }}" class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded font-medium text-xs sm:text-sm flex items-center justify-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                Pagar Ahora
                            </a>
                        @else
                            <!-- Si el pago está completo o es gratuito, mostrar botones normales -->
                            <a href="{{ route('torneos.show', $torneo) }}" class="text-indigo-600 hover:text-indigo-700 font-medium text-xs sm:text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                Ver
                            </a>

                            <div class="flex gap-2">
                                @if($torneo->estado === 'borrador')
                                    <a href="{{ route('torneos.edit', $torneo) }}" class="text-blue-600 hover:text-blue-700 font-medium text-xs sm:text-sm flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </a>
                                @endif
                            </div>
                        @endif

                        @if(in_array($torneo->estado, ['borrador', 'cancelado']))
                        <form action="{{ route('torneos.destroy', $torneo) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este torneo? Esta acción no se puede deshacer.');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-700 font-medium text-xs sm:text-sm flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-indigo-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">No tienes torneos creados</h3>
                <p class="text-gray-600 text-sm sm:text-base mb-6">
                    Crea tu primer torneo y comienza a gestionar tus eventos deportivos de manera profesional.
                </p>
                <a href="{{ route('torneos.create') }}" class="inline-flex items-center px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Mi Primer Torneo
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
