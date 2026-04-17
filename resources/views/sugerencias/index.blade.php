@extends('layouts.dashboard')

@section('title', 'Mis Sugerencias')
@section('page-title', 'Mis Sugerencias')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Header con botón de crear -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-gray-600 text-sm sm:text-base">Envía tus sugerencias, reporta bugs o solicita soporte técnico</p>
        </div>
        <a href="{{ route('sugerencias.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 w-full sm:w-auto justify-center text-sm sm:text-base">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Enviar Nueva Sugerencia
        </a>
    </div>

    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-700">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if($sugerencias->count() > 0)
        <!-- Grid de Cards de Sugerencias -->
        <div class="grid grid-cols-1 gap-4">
            @foreach($sugerencias as $sugerencia)
                @php
                    $tipoConfig = [
                        'sugerencia' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z', 'label' => 'Sugerencia'],
                        'soporte' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z', 'label' => 'Soporte'],
                        'bug' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z', 'label' => 'Bug'],
                        'otro' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z', 'label' => 'Otro'],
                    ];
                    $estadoConfig = [
                        'nueva' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'label' => 'Nueva'],
                        'en_revision' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'label' => 'En Revisión'],
                        'respondida' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'label' => 'Respondida'],
                        'cerrada' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'label' => 'Cerrada'],
                    ];
                    $tipo = $tipoConfig[$sugerencia->tipo] ?? $tipoConfig['otro'];
                    $estado = $estadoConfig[$sugerencia->estado] ?? $estadoConfig['nueva'];
                @endphp

                <div class="bg-white rounded-lg shadow-sm hover:shadow-md transition-shadow border border-gray-200">
                    <div class="p-4">
                        <!-- Header con tipo y estado -->
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex items-center space-x-2">
                                <div class="{{ $tipo['bg'] }} {{ $tipo['text'] }} p-2 rounded-lg">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tipo['icon'] }}"></path>
                                    </svg>
                                </div>
                                <span class="px-2.5 py-1 text-xs font-semibold rounded {{ $tipo['bg'] }} {{ $tipo['text'] }}">
                                    {{ $tipo['label'] }}
                                </span>
                            </div>
                            <span class="px-2.5 py-1 text-xs font-semibold rounded {{ $estado['bg'] }} {{ $estado['text'] }}">
                                {{ $estado['label'] }}
                            </span>
                        </div>

                        <!-- Asunto -->
                        <h3 class="text-base font-semibold text-gray-900 mb-2 line-clamp-1">
                            {{ $sugerencia->asunto }}
                        </h3>

                        <!-- Mensaje preview -->
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">
                            {{ $sugerencia->mensaje }}
                        </p>

                        <!-- Footer con fecha y botón -->
                        <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                            <div class="flex items-center text-sm text-gray-500">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                {{ $sugerencia->created_at->format('d/m/Y') }}
                            </div>
                            <a href="{{ route('sugerencias.show', $sugerencia) }}" class="inline-flex items-center text-sm font-medium text-brand-600 hover:text-brand-700">
                                Ver detalles
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>

                        <!-- Indicador de respuesta -->
                        @if($sugerencia->estado === 'respondida' && $sugerencia->respuesta)
                            <div class="mt-3 pt-3 border-t border-gray-100">
                                <div class="flex items-center text-sm text-green-600">
                                    <svg class="w-4 h-4 mr-1.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="font-medium">¡Tienes una respuesta del equipo!</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Paginación -->
        @if($sugerencias->hasPages())
            <div class="mt-6">
                {{ $sugerencias->links() }}
            </div>
        @endif
    @else
        <!-- Estado vacío -->
        <div class="bg-white rounded-lg shadow-sm p-8 sm:p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                </div>
                <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">No has enviado ninguna sugerencia</h3>
                <p class="text-gray-600 text-sm sm:text-base mb-6">
                    Nos encantaría escuchar tus ideas para mejorar la plataforma, resolver dudas o reportar problemas.
                </p>
                <a href="{{ route('sugerencias.create') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Enviar Mi Primera Sugerencia
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
