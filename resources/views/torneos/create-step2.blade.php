@extends('layouts.dashboard')

@section('title', 'Crear Torneo - Paso 2')
@section('page-title', 'Crear Nuevo Torneo')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Progress Bar -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-brand-600">Paso 2 de 2</span>
            <span class="text-sm text-gray-500">Formato del Torneo</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-brand-600 h-2 rounded-full transition-all duration-500" style="width: 100%"></div>
        </div>
    </div>

    <!-- Información del torneo -->
    <div class="bg-brand-50 border border-brand-200 rounded-lg p-4 mb-6">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1">
                <h3 class="text-sm font-semibold text-brand-900 mb-1">{{ $torneo->nombre }}</h3>
                <p class="text-xs text-brand-700">
                    {{ $torneo->deporte->nombre }} • {{ $torneo->complejo->nombre }} •
                    {{ $torneo->fecha_inicio->format('d/m/Y') }} al {{ $torneo->fecha_fin->format('d/m/Y') }}
                </p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 md:p-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Formato del Torneo</h2>

        <form action="{{ route('torneos.store-step2', $torneo) }}" method="POST" id="formatoForm">
            @csrf

            <!-- Formato del Torneo -->
            <div class="mb-6">
                <label for="formato_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Selecciona el Formato <span class="text-red-500">*</span>
                </label>
                <div class="space-y-3">
                    @foreach($formatos as $formato)
                        <label class="relative flex items-start p-4 border-2 rounded-lg cursor-pointer hover:bg-gray-50 transition formato-option {{ old('formato_id') == $formato->id ? 'border-brand-600 bg-brand-50' : 'border-gray-300' }}"
                               data-tiene-grupos="{{ $formato->tiene_grupos ? 'true' : 'false' }}">
                            <input
                                type="radio"
                                name="formato_id"
                                value="{{ $formato->id }}"
                                class="formato-radio mt-1"
                                {{ old('formato_id') == $formato->id ? 'checked' : '' }}
                                required
                            >
                            <div class="ml-3 flex-1">
                                <span class="block text-sm font-semibold text-gray-900">{{ $formato->nombre }}</span>
                                <span class="block text-xs text-gray-500 mt-1">
                                    @if($formato->tiene_grupos)
                                        Los equipos se dividen en grupos, juegan todos contra todos en su grupo, y luego los mejores avanzan a fase de eliminación directa.
                                    @else
                                        @if($formato->esLiga())
                                            Todos los equipos juegan contra todos. El que más puntos obtiene gana el torneo.
                                        @else
                                            Los equipos se enfrentan en llaves de eliminación directa. El perdedor queda eliminado.
                                        @endif
                                    @endif
                                </span>
                            </div>
                        </label>
                    @endforeach
                </div>
                @error('formato_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Integración DUPR -->
            <div class="mb-6" x-data="{ duprRequerido: {{ old('dupr_requerido', $torneo->dupr_requerido ?? false) ? 'true' : 'false' }} }">
                <div class="border border-gray-200 rounded-lg p-4 bg-gray-50">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-semibold text-gray-800">Integración DUPR</h3>
                            <p class="text-xs text-gray-500 mt-0.5">Si lo activás, solo jugadores con cuenta DUPR vinculada podrán inscribirse.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="dupr_requerido" value="1" class="sr-only peer"
                                   x-model="duprRequerido"
                                   {{ old('dupr_requerido', $torneo->dupr_requerido ?? false) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-brand-500 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                        </label>
                    </div>

                    <div x-show="duprRequerido" x-cloak class="mt-3 text-xs text-brand-700 bg-brand-50 border border-brand-200 rounded p-3">
                        Los campos de rating mínimo y máximo en cada categoría serán opcionales. Podés dejarlos en blanco para aceptar cualquier rating DUPR.
                    </div>
                </div>
            </div>

            <!-- Configuración de Cupos por Categoría (Liga/Eliminación Directa) -->
            <div id="configuracion-cupos" class="hidden space-y-6">
                <div class="bg-blue-50 border border-brand-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Configuración de Equipos por Categoría
                    </h3>
                    <p class="text-xs text-blue-700" id="descripcion-cupos">
                        Define cuántos equipos participarán en cada categoría.
                    </p>
                </div>

                @foreach($torneo->categorias as $index => $categoria)
                <div class="border border-gray-300 rounded-lg p-4 sm:p-6 bg-gray-50">
                    <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-bold mr-2">
                            {{ $index + 1 }}
                        </span>
                        Categoría: {{ $categoria->nombre }}
                    </h4>

                    <input type="hidden" name="categorias[{{ $index }}][categoria_id]" value="{{ $categoria->id }}">

                    <div>
                        <label for="cupos_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">
                            Cantidad de Equipos <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="number"
                            id="cupos_{{ $index }}"
                            name="categorias[{{ $index }}][cupos_categoria]"
                            min="2"
                            max="32"
                            value="{{ old('categorias.'.$index.'.cupos_categoria', 8) }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 cupos-input @error('categorias.'.$index.'.cupos_categoria') border-red-500 @enderror"
                            placeholder="Ej: 8"
                            data-index="{{ $index }}"
                        >
                        @error('categorias.'.$index.'.cupos_categoria')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-xs text-gray-500">
                            Mínimo 2, máximo 32 equipos
                        </p>
                    </div>

                    <!-- Restricciones de acceso (opcionales) -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Restricciones de acceso <span class="font-normal normal-case">(opcional)</span></p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="edad_min_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Edad mínima</label>
                                <input
                                    type="number"
                                    id="edad_min_{{ $index }}"
                                    name="categorias[{{ $index }}][edad_minima]"
                                    min="1" max="99"
                                    value="{{ old('categorias.'.$index.'.edad_minima') }}"
                                    placeholder="Ej: 18"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 text-sm @error('categorias.'.$index.'.edad_minima') border-red-500 @enderror"
                                >
                                @error('categorias.'.$index.'.edad_minima')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="edad_max_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Edad máxima</label>
                                <input
                                    type="number"
                                    id="edad_max_{{ $index }}"
                                    name="categorias[{{ $index }}][edad_maxima]"
                                    min="1" max="99"
                                    value="{{ old('categorias.'.$index.'.edad_maxima') }}"
                                    placeholder="Ej: 45"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 text-sm @error('categorias.'.$index.'.edad_maxima') border-red-500 @enderror"
                                >
                                @error('categorias.'.$index.'.edad_maxima')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="genero_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Género permitido</label>
                                <select
                                    id="genero_{{ $index }}"
                                    name="categorias[{{ $index }}][genero_permitido]"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 text-sm bg-white @error('categorias.'.$index.'.genero_permitido') border-red-500 @enderror"
                                >
                                    <option value="">Sin restricción</option>
                                    <option value="masculino" {{ old('categorias.'.$index.'.genero_permitido') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('categorias.'.$index.'.genero_permitido') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="mixto" {{ old('categorias.'.$index.'.genero_permitido') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                                </select>
                                @error('categorias.'.$index.'.genero_permitido')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- DUPR rating min/max (visible solo si dupr_requerido está activo) --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 dupr-rating-fields hidden">
                            <div>
                                <label for="dupr_rating_min_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Rating DUPR mínimo (opcional)</label>
                                <input type="number" step="0.01" min="2" max="8"
                                       id="dupr_rating_min_{{ $index }}"
                                       name="categorias[{{ $index }}][dupr_rating_min]"
                                       value="{{ old('categorias.'.$index.'.dupr_rating_min', $categoria->pivot->dupr_rating_min ?? '') }}"
                                       placeholder="Ej: 3.50"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500">
                                @error('categorias.'.$index.'.dupr_rating_min')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="dupr_rating_max_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Rating DUPR máximo (opcional)</label>
                                <input type="number" step="0.01" min="2" max="8"
                                       id="dupr_rating_max_{{ $index }}"
                                       name="categorias[{{ $index }}][dupr_rating_max]"
                                       value="{{ old('categorias.'.$index.'.dupr_rating_max', $categoria->pivot->dupr_rating_max ?? '') }}"
                                       placeholder="Ej: 5.00"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500">
                                @error('categorias.'.$index.'.dupr_rating_max')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Preview de partidos/rondas -->
                    <div class="mt-3 text-xs text-gray-600 bg-white p-2 rounded border border-gray-200">
                        💡 <strong>Ejemplo:</strong>
                        <span class="cupos-preview" data-index="{{ $index }}" data-formato="">
                            8 equipos
                        </span>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Configuración de Grupos por Categoría (solo si tiene grupos) -->
            <div id="configuracion-grupos" class="hidden space-y-6">
                <div class="bg-blue-50 border border-brand-200 rounded-lg p-4">
                    <h3 class="text-sm font-semibold text-blue-900 mb-2 flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path>
                        </svg>
                        Configuración de Fase de Grupos por Categoría
                    </h3>
                    <p class="text-xs text-blue-700">
                        Define la configuración de grupos para cada categoría del torneo. Cada categoría puede tener diferente cantidad de grupos y equipos.
                    </p>
                </div>

                <!-- Configuración por cada categoría -->
                @foreach($torneo->categorias as $index => $categoria)
                <div class="border border-gray-300 rounded-lg p-4 sm:p-6 bg-gray-50">
                    <h4 class="text-base font-semibold text-gray-800 mb-4 flex items-center">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-brand-600 text-white text-xs font-bold mr-2">
                            {{ $index + 1 }}
                        </span>
                        Categoría: {{ $categoria->nombre }}
                    </h4>

                    <!-- Hidden input para categoria_id -->
                    <input type="hidden" name="categorias[{{ $index }}][categoria_id]" value="{{ $categoria->id }}">

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <!-- Número de Grupos -->
                        <div>
                            <label for="numero_grupos_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">
                                Nº de Grupos <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                id="numero_grupos_{{ $index }}"
                                name="categorias[{{ $index }}][numero_grupos]"
                                min="2"
                                max="8"
                                value="{{ old('categorias.'.$index.'.numero_grupos') }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 @error('categorias.'.$index.'.numero_grupos') border-red-500 @enderror"
                                placeholder="Ej: 4"
                            >
                            @error('categorias.'.$index.'.numero_grupos')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Tamaño de Grupo -->
                        <div>
                            <label for="tamanio_grupo_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">
                                Equipos/Grupo <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="tamanio_grupo_{{ $index }}"
                                name="categorias[{{ $index }}][tamanio_grupo_id]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 @error('categorias.'.$index.'.tamanio_grupo_id') border-red-500 @enderror"
                            >
                                <option value="">Seleccionar</option>
                                @foreach($tamanios as $tamanio)
                                    <option value="{{ $tamanio->id }}" {{ old('categorias.'.$index.'.tamanio_grupo_id') == $tamanio->id ? 'selected' : '' }}>
                                        {{ $tamanio->tamanio }} equipos
                                    </option>
                                @endforeach
                            </select>
                            @error('categorias.'.$index.'.tamanio_grupo_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Avance de Grupos -->
                        <div>
                            <label for="avance_grupos_{{ $index }}" class="block text-sm font-medium text-gray-700 mb-1">
                                Quiénes Avanzan <span class="text-red-500">*</span>
                            </label>
                            <select
                                id="avance_grupos_{{ $index }}"
                                name="categorias[{{ $index }}][avance_grupos_id]"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 @error('categorias.'.$index.'.avance_grupos_id') border-red-500 @enderror"
                            >
                                <option value="">Seleccionar</option>
                                @foreach($avances as $avance)
                                    <option value="{{ $avance->id }}" {{ old('categorias.'.$index.'.avance_grupos_id') == $avance->id ? 'selected' : '' }}>
                                        {{ $avance->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categorias.'.$index.'.avance_grupos_id')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Restricciones de acceso (opcionales) -->
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Restricciones de acceso <span class="font-normal normal-case">(opcional)</span></p>
                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                            <div>
                                <label for="edad_min_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Edad mínima</label>
                                <input
                                    type="number"
                                    id="edad_min_{{ $index }}"
                                    name="categorias[{{ $index }}][edad_minima]"
                                    min="1" max="99"
                                    value="{{ old('categorias.'.$index.'.edad_minima') }}"
                                    placeholder="Ej: 18"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 text-sm @error('categorias.'.$index.'.edad_minima') border-red-500 @enderror"
                                >
                                @error('categorias.'.$index.'.edad_minima')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="edad_max_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Edad máxima</label>
                                <input
                                    type="number"
                                    id="edad_max_{{ $index }}"
                                    name="categorias[{{ $index }}][edad_maxima]"
                                    min="1" max="99"
                                    value="{{ old('categorias.'.$index.'.edad_maxima') }}"
                                    placeholder="Ej: 45"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 text-sm @error('categorias.'.$index.'.edad_maxima') border-red-500 @enderror"
                                >
                                @error('categorias.'.$index.'.edad_maxima')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            <div>
                                <label for="genero_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Género permitido</label>
                                <select
                                    id="genero_{{ $index }}"
                                    name="categorias[{{ $index }}][genero_permitido]"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 text-sm bg-white @error('categorias.'.$index.'.genero_permitido') border-red-500 @enderror"
                                >
                                    <option value="">Sin restricción</option>
                                    <option value="masculino" {{ old('categorias.'.$index.'.genero_permitido') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                    <option value="femenino" {{ old('categorias.'.$index.'.genero_permitido') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                    <option value="mixto" {{ old('categorias.'.$index.'.genero_permitido') == 'mixto' ? 'selected' : '' }}>Mixto</option>
                                </select>
                                @error('categorias.'.$index.'.genero_permitido')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- DUPR rating min/max --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 dupr-rating-fields hidden">
                            <div>
                                <label for="gr_dupr_rating_min_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Rating DUPR mínimo (opcional)</label>
                                <input type="number" step="0.01" min="2" max="8"
                                       id="gr_dupr_rating_min_{{ $index }}"
                                       name="categorias[{{ $index }}][dupr_rating_min]"
                                       value="{{ old('categorias.'.$index.'.dupr_rating_min', $categoria->pivot->dupr_rating_min ?? '') }}"
                                       placeholder="Ej: 3.50"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500">
                            </div>
                            <div>
                                <label for="gr_dupr_rating_max_{{ $index }}" class="block text-xs font-medium text-gray-600 mb-1">Rating DUPR máximo (opcional)</label>
                                <input type="number" step="0.01" min="2" max="8"
                                       id="gr_dupr_rating_max_{{ $index }}"
                                       name="categorias[{{ $index }}][dupr_rating_max]"
                                       value="{{ old('categorias.'.$index.'.dupr_rating_max', $categoria->pivot->dupr_rating_max ?? '') }}"
                                       placeholder="Ej: 5.00"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500">
                            </div>
                        </div>
                    </div>

                    <!-- Resumen de cupos para esta categoría -->
                    <div class="mt-3 text-xs text-gray-600 bg-white p-2 rounded border border-gray-200">
                        💡 <strong>Ejemplo:</strong> 4 grupos × 2 equipos = 8 cupos totales para categoría {{ $categoria->nombre }}
                    </div>
                </div>
                @endforeach
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-6 h-6 text-green-600 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-green-900 mb-1">Vista Previa del Formato</h4>
                                <p id="preview-text" class="text-sm text-green-800"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('torneos.create') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-center text-sm sm:text-base order-2 sm:order-1">
                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Volver al Paso 1
                </a>
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base order-1 sm:order-2">
                    <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Finalizar y Crear Torneo
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formatoRadios = document.querySelectorAll('.formato-radio');
    const configuracionGrupos = document.getElementById('configuracion-grupos');
    const configuracionCupos = document.getElementById('configuracion-cupos');
    const tamanioSelect = document.getElementById('tamanio_grupo_id');
    const avanceSelect = document.getElementById('avance_grupos_id');
    const previewDiv = document.getElementById('preview-avance');
    const previewText = document.getElementById('preview-text');

    // Deshabilita/habilita inputs de un contenedor para evitar que se envíen valores duplicados
    function setInputsDisabled(container, disabled) {
        container.querySelectorAll('input, select').forEach(el => {
            el.disabled = disabled;
        });
    }

    // Ambas secciones empiezan ocultas: deshabilitar sus inputs
    setInputsDisabled(configuracionGrupos, true);
    setInputsDisabled(configuracionCupos, true);

    // Manejar cambio de formato
    formatoRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const option = this.closest('.formato-option');
            const tieneGrupos = option.dataset.tieneGrupos === 'true';
            const formatoNombre = option.querySelector('.text-sm.font-semibold').textContent.trim();
            const esLiga = formatoNombre.includes('Liga');
            const esEliminacion = formatoNombre.includes('Eliminación Directa') && !formatoNombre.includes('Fase de Grupos');

            // Actualizar estilos visuales
            document.querySelectorAll('.formato-option').forEach(opt => {
                opt.classList.remove('border-brand-600', 'bg-brand-50');
                opt.classList.add('border-gray-300');
            });
            option.classList.remove('border-gray-300');
            option.classList.add('border-brand-600', 'bg-brand-50');

            // Mostrar/ocultar configuración según formato, habilitando solo la sección visible
            if (tieneGrupos) {
                configuracionGrupos.classList.remove('hidden');
                setInputsDisabled(configuracionGrupos, false);
                configuracionCupos.classList.add('hidden');
                setInputsDisabled(configuracionCupos, true);
                if (tamanioSelect) { tamanioSelect.setAttribute('required', 'required'); }
                if (avanceSelect) { avanceSelect.setAttribute('required', 'required'); }
            } else {
                configuracionGrupos.classList.add('hidden');
                setInputsDisabled(configuracionGrupos, true);
                configuracionCupos.classList.remove('hidden');
                setInputsDisabled(configuracionCupos, false);
                if (tamanioSelect) { tamanioSelect.removeAttribute('required'); tamanioSelect.value = ''; }
                if (avanceSelect) { avanceSelect.removeAttribute('required'); avanceSelect.value = ''; }
                previewDiv.classList.add('hidden');

                // Actualizar descripción según formato
                const descripcionCupos = document.getElementById('descripcion-cupos');
                if (esLiga) {
                    descripcionCupos.textContent = 'Define cuántos equipos participarán en cada categoría. Todos jugarán entre sí (todos contra todos).';
                } else if (esEliminacion) {
                    descripcionCupos.textContent = 'Define cuántos equipos participarán en cada categoría. Competirán en llaves de eliminación directa.';
                } else {
                    descripcionCupos.textContent = 'Define cuántos equipos participarán en cada categoría.';
                }

                // Actualizar formato en los previews y recalcular
                document.querySelectorAll('.cupos-preview').forEach(preview => {
                    preview.dataset.formato = esLiga ? 'rr' : (esEliminacion ? 'ed' : '');
                });

                // Recalcular todos los previews
                cuposInputs.forEach(input => actualizarPreviewCupos(input));
            }
        });
    });

    // Calcular y mostrar preview cuando cambian los selects
    const numeroGruposInput = document.getElementById('numero_grupos');

    function actualizarPreview() {
        const avanceSelected = avanceSelect.options[avanceSelect.selectedIndex];
        const numeroGrupos = parseInt(numeroGruposInput.value) || 0;

        if (avanceSelect.value && tamanioSelect.value && numeroGrupos > 0) {
            const directos = parseInt(avanceSelected.dataset.directos) || 0;
            const mejores = parseInt(avanceSelected.dataset.mejores) || 0;
            const equiposPorDirecto = directos * numeroGrupos;
            const totalAvanzan = equiposPorDirecto + mejores;

            let mensaje = `Con ${numeroGrupos} grupos: `;

            if (directos > 0 && mejores === 0) {
                mensaje += `Avanzan ${directos} equipo${directos > 1 ? 's' : ''} de cada grupo = <strong>${totalAvanzan} equipos</strong> a fase de eliminación.`;
            } else if (directos > 0 && mejores > 0) {
                mensaje += `Avanzan ${directos} equipo${directos > 1 ? 's' : ''} de cada grupo (${equiposPorDirecto} equipos) + los ${mejores} mejores segundos = <strong>${totalAvanzan} equipos</strong> a fase de eliminación.`;
            } else {
                mensaje += `Avanzan <strong>${totalAvanzan} equipos</strong> a fase de eliminación.`;
            }

            previewText.innerHTML = mensaje;
            previewDiv.classList.remove('hidden');
        } else {
            previewDiv.classList.add('hidden');
        }
    }

    avanceSelect.addEventListener('change', actualizarPreview);
    tamanioSelect.addEventListener('change', actualizarPreview);
    numeroGruposInput.addEventListener('input', actualizarPreview);

    // Calcular y mostrar preview de partidos para Liga o rondas para ED
    const cuposInputs = document.querySelectorAll('.cupos-input');

    function calcularPartidosLiga(numEquipos) {
        return (numEquipos * (numEquipos - 1)) / 2;
    }

    function calcularRondasEliminacion(numEquipos) {
        if (numEquipos < 2) return 0;
        return Math.ceil(Math.log2(numEquipos));
    }

    function actualizarPreviewCupos(input) {
        const index = input.dataset.index;
        const numEquipos = parseInt(input.value) || 0;
        const previewSpan = document.querySelector(`.cupos-preview[data-index="${index}"]`);
        const formato = previewSpan.dataset.formato;

        if (numEquipos >= 2) {
            if (formato === 'rr') {
                // Liga: mostrar partidos totales
                const partidos = calcularPartidosLiga(numEquipos);
                previewSpan.textContent = `${numEquipos} equipos = ${partidos} partidos totales`;
            } else if (formato === 'ed') {
                // Eliminación Directa: mostrar rondas necesarias
                const rondas = calcularRondasEliminacion(numEquipos);
                const equiposPotencia = Math.pow(2, rondas);
                const byes = equiposPotencia - numEquipos;

                let mensaje = `${numEquipos} equipos = ${rondas} ronda${rondas > 1 ? 's' : ''}`;
                if (byes > 0) {
                    mensaje += ` (${byes} BYE${byes > 1 ? 's' : ''})`;
                }
                previewSpan.textContent = mensaje;
            } else {
                previewSpan.textContent = `${numEquipos} equipos`;
            }
        } else {
            if (formato === 'rr') {
                previewSpan.textContent = '8 equipos = 28 partidos totales';
            } else if (formato === 'ed') {
                previewSpan.textContent = '8 equipos = 3 rondas';
            } else {
                previewSpan.textContent = '8 equipos';
            }
        }
    }

    cuposInputs.forEach(input => {
        input.addEventListener('input', function() {
            actualizarPreviewCupos(this);
        });

        // Inicializar preview al cargar
        actualizarPreviewCupos(input);
    });

    // Verificar si hay un formato preseleccionado (old input)
    const formatoChecked = document.querySelector('.formato-radio:checked');
    if (formatoChecked) {
        formatoChecked.dispatchEvent(new Event('change'));
        actualizarPreview();
    }

    // Toggle DUPR rating fields
    function toggleDuprRatingFields(checked) {
        document.querySelectorAll('.dupr-rating-fields').forEach(el => {
            el.classList.toggle('hidden', !checked);
        });
    }

    const duprCheckbox = document.querySelector('input[name="dupr_requerido"]');
    if (duprCheckbox) {
        duprCheckbox.addEventListener('change', () => toggleDuprRatingFields(duprCheckbox.checked));
        toggleDuprRatingFields(duprCheckbox.checked);
    }
});
</script>
@endsection
