@extends('admin.layouts.app')

@section('title', 'Detalle de Sugerencia')
@section('page-title', 'Detalle de Sugerencia')

@section('content')
<div class="space-y-6">
    <!-- Botón Volver -->
    <div>
        <a href="{{ route('admin.sugerencias.index') }}" class="inline-flex items-center text-brand-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver a Sugerencias
        </a>
    </div>

    <!-- Información de la Sugerencia -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Detalles de la Sugerencia -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Información Principal -->
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-start justify-between mb-4">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-2">{{ $sugerencia->asunto }}</h2>
                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                            <span>
                                <i class="far fa-calendar mr-1"></i>
                                {{ $sugerencia->created_at->format('d/m/Y H:i') }}
                            </span>
                            <span>
                                @php
                                    $tipoBadges = [
                                        'sugerencia' => ['blue', 'fa-lightbulb', 'Sugerencia'],
                                        'soporte' => ['yellow', 'fa-headset', 'Soporte'],
                                        'bug' => ['red', 'fa-bug', 'Bug'],
                                        'otro' => ['gray', 'fa-question', 'Otro']
                                    ];
                                    $tipo = $tipoBadges[$sugerencia->tipo] ?? ['gray', 'fa-circle', ucfirst($sugerencia->tipo)];
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $tipo[0] }}-100 text-{{ $tipo[0] }}-800">
                                    <i class="fas {{ $tipo[1] }} mr-1"></i>{{ $tipo[2] }}
                                </span>
                            </span>
                            <span>
                                @php
                                    $estadoBadges = [
                                        'nueva' => ['yellow', 'Nueva'],
                                        'en_revision' => ['blue', 'En Revisión'],
                                        'respondida' => ['green', 'Respondida'],
                                        'cerrada' => ['gray', 'Cerrada']
                                    ];
                                    $estado = $estadoBadges[$sugerencia->estado] ?? ['gray', ucfirst($sugerencia->estado)];
                                @endphp
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-{{ $estado[0] }}-100 text-{{ $estado[0] }}-800">
                                    {{ $estado[1] }}
                                </span>
                            </span>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-200 pt-4">
                    <h3 class="text-sm font-semibold text-gray-700 mb-2">Mensaje:</h3>
                    <div class="prose prose-sm max-w-none text-gray-700 bg-gray-50 p-4 rounded-lg">
                        {{ $sugerencia->mensaje }}
                    </div>
                </div>
            </div>

            <!-- Respuesta (si existe) -->
            @if($sugerencia->respuesta)
                <div class="bg-green-50 border border-green-200 rounded-lg shadow p-6">
                    <div class="flex items-start justify-between mb-4">
                        <h3 class="text-lg font-semibold text-green-800">
                            <i class="fas fa-reply mr-2"></i>
                            Respuesta del Administrador
                        </h3>
                        @if($sugerencia->respondida_por && $sugerencia->respondidaPor)
                            <span class="text-sm text-green-600">
                                Por: {{ $sugerencia->respondidaPor->name }}
                            </span>
                        @endif
                    </div>
                    <div class="prose prose-sm max-w-none text-gray-700 bg-white p-4 rounded-lg">
                        {{ $sugerencia->respuesta }}
                    </div>
                    <p class="text-xs text-green-600 mt-2">
                        <i class="far fa-clock mr-1"></i>
                        Respondida el {{ $sugerencia->respondida_en?->format('d/m/Y H:i') ?? 'N/A' }}
                    </p>
                </div>
            @endif

            <!-- Formulario para Responder -->
            @if(!$sugerencia->respuesta)
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        <i class="fas fa-reply text-brand-600 mr-2"></i>
                        Responder Sugerencia
                    </h3>
                    <form action="{{ route('admin.sugerencias.responder', $sugerencia) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Respuesta <span class="text-red-500">*</span>
                            </label>
                            <textarea name="respuesta" rows="6" required placeholder="Escribe tu respuesta al usuario..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="bg-brand-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                                <i class="fas fa-paper-plane mr-2"></i>
                                Enviar Respuesta
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Información del Usuario -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-user text-brand-600 mr-2"></i>
                    Usuario
                </h3>
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 rounded-full bg-blue-500 flex items-center justify-center text-white text-xl font-bold">
                        {{ strtoupper(substr($sugerencia->user->nombre, 0, 1))}}{{ strtoupper(substr($sugerencia->user->apellido, 0, 1)) }}
                    </div>
                    <div class="ml-3">
                        <div class="text-sm font-medium text-gray-900">
                            {{ $sugerencia->user->nombre }} {{ $sugerencia->user->apellido }}
                        </div>
                        <div class="text-xs text-gray-500">{{ $sugerencia->user->email }}</div>
                    </div>
                </div>
                <div class="border-t border-gray-200 pt-4 space-y-2">
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Torneos Creados:</span>
                        <span class="font-semibold text-gray-800">{{ $sugerencia->user->torneos->count() }}</span>
                    </div>
                    <div class="flex items-center justify-between text-sm">
                        <span class="text-gray-600">Registrado:</span>
                        <span class="font-semibold text-gray-800">{{ $sugerencia->user->created_at->format('d/m/Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Cambiar Estado -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">
                    <i class="fas fa-exchange-alt text-brand-600 mr-2"></i>
                    Cambiar Estado
                </h3>
                <form action="{{ route('admin.sugerencias.cambiar-estado', $sugerencia) }}" method="POST">
                    @csrf
                    <div class="space-y-3">
                        @foreach(['nueva' => ['yellow', 'Nueva'], 'en_revision' => ['blue', 'En Revisión'], 'respondida' => ['green', 'Respondida'], 'cerrada' => ['gray', 'Cerrada']] as $estadoKey => $estadoData)
                            <label class="flex items-center p-3 border-2 rounded-lg cursor-pointer transition {{ $sugerencia->estado === $estadoKey ? 'border-' . $estadoData[0] . '-500 bg-' . $estadoData[0] . '-50' : 'border-gray-200 hover:border-gray-300' }}">
                                <input type="radio" name="estado" value="{{ $estadoKey }}" {{ $sugerencia->estado === $estadoKey ? 'checked' : '' }} class="mr-3">
                                <span class="font-medium text-gray-800">{{ $estadoData[1] }}</span>
                            </label>
                        @endforeach
                    </div>
                    <button type="submit" class="mt-4 w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition">
                        <i class="fas fa-save mr-2"></i>
                        Actualizar Estado
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
