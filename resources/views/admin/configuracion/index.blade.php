@extends('admin.layouts.app')

@section('title', 'Configuración del Sistema')
@section('page-title', 'Configuración del Sistema')

@section('content')
<div class="space-y-6">
    <!-- Header con botón para limpiar caché -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white rounded-lg shadow p-4 md:p-6">
        <div>
            <h2 class="text-lg md:text-xl font-semibold text-gray-800">Configuraciones Generales</h2>
            <p class="text-sm text-gray-600 mt-1">Administra los valores de configuración del sistema</p>
        </div>
        <form action="{{ route('admin.configuracion.clear-cache') }}" method="POST" class="w-full sm:w-auto">
            @csrf
            <button type="submit"
                    class="w-full sm:w-auto bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center justify-center gap-2"
                    onclick="return confirm('¿Limpiar caché de configuraciones?')">
                <i class="fas fa-sync-alt"></i>
                <span>Limpiar Caché</span>
            </button>
        </form>
    </div>

    <!-- Alertas de éxito/error -->
    @if(session('success'))
        <div class="bg-green-50 border-l-4 border-green-400 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <p class="text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 text-xl mr-3"></i>
                <p class="text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex items-start">
                <i class="fas fa-exclamation-triangle text-red-600 text-xl mr-3 mt-0.5"></i>
                <div>
                    <p class="text-red-800 font-medium">Errores de validación:</p>
                    <ul class="list-disc list-inside text-red-700 text-sm mt-1">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- Categorías de Configuración -->
    @foreach($configuracionesAgrupadas as $key => $grupo)
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <!-- Header de Categoría -->
            <div class="bg-gradient-to-r from-{{ $grupo['info']['color'] }}-500 to-{{ $grupo['info']['color'] }}-600 px-4 md:px-6 py-4">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center mr-3">
                        <i class="fas fa-{{ $grupo['info']['icono'] }} text-white text-lg"></i>
                    </div>
                    <div>
                        <h3 class="text-white font-semibold text-base md:text-lg">{{ $grupo['info']['titulo'] }}</h3>
                        <p class="text-white/90 text-xs md:text-sm">{{ $grupo['info']['descripcion'] }}</p>
                    </div>
                </div>
            </div>

            <!-- Configuraciones de la Categoría -->
            <div class="divide-y divide-gray-200">
                @forelse($grupo['configs'] as $config)
                    <div class="p-4 md:p-6 hover:bg-gray-50 transition">
                        <form action="{{ route('admin.configuracion.update', $config) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')

                            <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                                <!-- Información de la Configuración -->
                                <div class="flex-1">
                                    <div class="flex items-start">
                                        <div class="flex-1">
                                            <label class="block text-sm md:text-base font-semibold text-gray-800 mb-1">
                                                {{ $config->clave }}
                                            </label>
                                            @if($config->descripcion)
                                                <p class="text-xs md:text-sm text-gray-600 mb-3">
                                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                                    {{ $config->descripcion }}
                                                </p>
                                            @endif

                                            <!-- Input según tipo -->
                                            <div class="mt-2">
                                                @if($config->tipo === 'boolean')
                                                    <div class="flex items-center gap-3">
                                                        <label class="relative inline-flex items-center cursor-pointer">
                                                            <input type="checkbox"
                                                                   name="valor"
                                                                   value="1"
                                                                   {{ $config->valor_parsed ? 'checked' : '' }}
                                                                   class="sr-only peer">
                                                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                                                        </label>
                                                        <span class="text-sm text-gray-600">
                                                            {{ $config->valor_parsed ? 'Activado' : 'Desactivado' }}
                                                        </span>
                                                    </div>
                                                @elseif($config->tipo === 'integer')
                                                    <div class="flex items-center gap-3">
                                                        <input type="number"
                                                               name="valor"
                                                               value="{{ old('valor', $config->valor) }}"
                                                               step="1"
                                                               min="0"
                                                               @if(str_contains($config->clave, 'porcentaje')) max="100" @endif
                                                               class="block w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-{{ $grupo['info']['color'] }}-500 focus:border-transparent"
                                                               required>
                                                        @if(str_contains($config->clave, 'porcentaje'))
                                                            <span class="text-gray-600 font-medium">%</span>
                                                        @endif
                                                    </div>
                                                @elseif($config->tipo === 'decimal')
                                                    <div class="flex items-center gap-3">
                                                        <span class="text-gray-600 font-medium">$</span>
                                                        <input type="number"
                                                               name="valor"
                                                               value="{{ old('valor', $config->valor) }}"
                                                               step="0.01"
                                                               min="0"
                                                               class="block w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-{{ $grupo['info']['color'] }}-500 focus:border-transparent"
                                                               required>
                                                    </div>
                                                @else
                                                    <input type="text"
                                                           name="valor"
                                                           value="{{ old('valor', $config->valor) }}"
                                                           class="block w-full md:w-96 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-{{ $grupo['info']['color'] }}-500 focus:border-transparent"
                                                           required>
                                                @endif

                                                <!-- Hint de tipo -->
                                                <p class="text-xs text-gray-500 mt-2">
                                                    <i class="fas fa-tag mr-1"></i>
                                                    Tipo: <span class="font-medium">{{ $config->tipo }}</span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Botón de Guardar -->
                                <div class="flex lg:flex-col items-center gap-2">
                                    <button type="submit"
                                            class="w-full lg:w-auto bg-{{ $grupo['info']['color'] }}-600 hover:bg-{{ $grupo['info']['color'] }}-700 text-white px-4 md:px-6 py-2 rounded-lg font-medium transition flex items-center justify-center gap-2">
                                        <i class="fas fa-save"></i>
                                        <span>Guardar</span>
                                    </button>
                                </div>
                            </div>

                            <!-- Valor Actual -->
                            <div class="bg-gray-50 rounded-lg p-3 mt-3">
                                <p class="text-xs text-gray-600">
                                    <span class="font-medium">Valor actual:</span>
                                    @if($config->tipo === 'boolean')
                                        <span class="ml-2 px-2 py-1 rounded {{ $config->valor_parsed ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                                            {{ $config->valor_parsed ? 'Activado' : 'Desactivado' }}
                                        </span>
                                    @elseif($config->tipo === 'decimal')
                                        <span class="ml-2 font-mono text-gray-800">${{ number_format($config->valor, 2, ',', '.') }}</span>
                                    @elseif(str_contains($config->clave, 'porcentaje'))
                                        <span class="ml-2 font-mono text-gray-800">{{ $config->valor }}%</span>
                                    @else
                                        <span class="ml-2 font-mono text-gray-800">{{ $config->valor }}</span>
                                    @endif
                                </p>
                            </div>
                        </form>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500">
                        <i class="fas fa-inbox text-4xl mb-2"></i>
                        <p>No hay configuraciones en esta categoría</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endforeach

    <!-- Información adicional -->
    <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded">
        <div class="flex items-start">
            <i class="fas fa-info-circle text-brand-600 text-xl mr-3 mt-0.5"></i>
            <div class="text-sm text-blue-800">
                <p class="font-medium mb-1">Información importante:</p>
                <ul class="list-disc list-inside space-y-1 text-blue-700">
                    <li>Los cambios se aplican inmediatamente en todo el sistema</li>
                    <li>Las configuraciones se almacenan en caché durante 24 horas</li>
                    <li>Puedes limpiar el caché manualmente si necesitas forzar una actualización</li>
                    <li>Ten cuidado al modificar valores críticos como precios</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
