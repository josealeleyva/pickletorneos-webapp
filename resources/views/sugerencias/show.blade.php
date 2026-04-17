@extends('layouts.dashboard')

@section('title', 'Detalle de Sugerencia')
@section('page-title', 'Detalle de Sugerencia')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Botón volver -->
    <div class="mb-4">
        <a href="{{ route('sugerencias.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 font-medium text-sm">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Volver al listado
        </a>
    </div>

    <!-- Card principal -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header con gradiente -->
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-500 to-purple-600">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-2">
                        @php
                            $tipoClasses = [
                                'sugerencia' => 'bg-blue-100 text-blue-800',
                                'soporte' => 'bg-yellow-100 text-yellow-800',
                                'bug' => 'bg-red-100 text-red-800',
                                'otro' => 'bg-gray-100 text-gray-800',
                            ];
                            $tipoTextos = [
                                'sugerencia' => 'Sugerencia',
                                'soporte' => 'Soporte',
                                'bug' => 'Bug',
                                'otro' => 'Otro',
                            ];
                        @endphp
                        <span class="px-3 py-1 text-sm font-semibold rounded {{ $tipoClasses[$sugerencia->tipo] ?? 'bg-gray-100 text-gray-800' }}">
                            {{ $tipoTextos[$sugerencia->tipo] ?? 'Desconocido' }}
                        </span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-bold text-white">{{ $sugerencia->asunto }}</h2>
                </div>
                <div>
                    @php
                        $estadoClasses = [
                            'pendiente' => 'bg-yellow-100 text-yellow-800 border-yellow-300',
                            'en_revision' => 'bg-blue-100 text-blue-800 border-blue-300',
                            'respondida' => 'bg-green-100 text-green-800 border-green-300',
                            'cerrada' => 'bg-gray-100 text-gray-800 border-gray-300',
                        ];
                        $estadoTextos = [
                            'pendiente' => 'Pendiente',
                            'en_revision' => 'En Revisión',
                            'respondida' => 'Respondida',
                            'cerrada' => 'Cerrada',
                        ];
                    @endphp
                    <span class="px-4 py-2 text-sm font-bold rounded-lg border-2 {{ $estadoClasses[$sugerencia->estado] ?? 'bg-gray-100 text-gray-800 border-gray-300' }}">
                        {{ $estadoTextos[$sugerencia->estado] ?? 'Desconocido' }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Información de la sugerencia -->
        <div class="p-6 space-y-6">
            <!-- Metadata -->
            <div class="flex flex-wrap gap-6 text-sm text-gray-600 pb-4 border-b border-gray-200">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <span><strong>Enviado:</strong> {{ $sugerencia->created_at->format('d/m/Y H:i') }}</span>
                </div>
                @if($sugerencia->created_at != $sugerencia->updated_at)
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        <span><strong>Actualizado:</strong> {{ $sugerencia->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>

            <!-- Mensaje del usuario -->
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                    </svg>
                    Tu mensaje
                </h3>
                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                    <p class="text-gray-700 whitespace-pre-line">{{ $sugerencia->mensaje }}</p>
                </div>
            </div>

            <!-- Respuesta del admin (si existe) -->
            @if($sugerencia->respuesta)
                <div class="pt-4 border-t border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                        </svg>
                        Respuesta del equipo
                    </h3>
                    <div class="bg-green-50 rounded-lg p-5 border-2 border-green-200">
                        <div class="flex items-start gap-3 mb-3">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                                    <p class="font-semibold text-gray-900">
                                        {{ $sugerencia->respondidoPor->name ?? 'Equipo de Punto de Oro' }}
                                    </p>
                                    @if($sugerencia->fecha_respuesta)
                                        <p class="text-sm text-gray-600">
                                            {{ $sugerencia->fecha_respuesta->format('d/m/Y H:i') }}
                                        </p>
                                    @endif
                                </div>
                                <p class="text-gray-700 whitespace-pre-line">{{ $sugerencia->respuesta }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Mensaje de pendiente -->
                <div class="pt-4 border-t border-gray-200">
                    <div class="bg-yellow-50 rounded-lg p-5 border-2 border-yellow-200">
                        <div class="flex items-start gap-3">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 text-yellow-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-yellow-900 mb-1">Pendiente de respuesta</h4>
                                <p class="text-sm text-yellow-700">
                                    Hemos recibido tu mensaje y nuestro equipo lo está revisando. Te responderemos a la brevedad posible. Gracias por tu paciencia.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Footer con acción -->
        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200">
            <div class="flex flex-col sm:flex-row justify-between items-center gap-4">
                <p class="text-sm text-gray-600">
                    ¿Tienes algo más que agregar?
                    <a href="{{ route('sugerencias.create') }}" class="text-indigo-600 hover:text-indigo-800 font-medium">
                        Envía otra sugerencia
                    </a>
                </p>
                <a href="{{ route('sugerencias.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-medium rounded-lg transition duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Volver al listado
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
