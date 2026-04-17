@extends('layouts.dashboard')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 sm:py-8">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center text-sm text-gray-600 mb-4">
            <a href="{{ route('torneos.index') }}" class="hover:text-indigo-600">Torneos</a>
            <svg class="h-4 w-4 mx-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
            </svg>
            <span class="text-gray-900">Editar Torneo</span>
        </div>
        <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">Editar Torneo</h1>
        <p class="mt-2 text-sm text-gray-600">Modifica la información de tu torneo</p>
    </div>

    <!-- Alert de borrador -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex">
            <svg class="h-5 w-5 text-blue-400 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
            </svg>
            <div class="ml-3">
                <p class="text-sm text-blue-700">
                    Este torneo está en estado <strong>borrador</strong>. Puedes modificar toda la información antes de publicarlo.
                </p>
            </div>
        </div>
    </div>

    <!-- Formulario de edición -->
    <form action="{{ route('torneos.update', $torneo) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Sección 1: Información General -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Información General</h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Nombre del Torneo -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre del Torneo <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        name="nombre"
                        id="nombre"
                        value="{{ old('nombre', $torneo->nombre) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nombre') border-red-500 @enderror"
                        placeholder="Ej: Torneo de Padel Verano 2024"
                    >
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Deporte -->
                <div>
                    <label for="deporte_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Deporte <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="deporte_id"
                        id="deporte_id"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('deporte_id') border-red-500 @enderror"
                    >
                        <option value="">Selecciona un deporte</option>
                        @foreach($deportes as $deporte)
                            <option value="{{ $deporte->id }}" {{ old('deporte_id', $torneo->deporte_id) == $deporte->id ? 'selected' : '' }}>
                                {{ $deporte->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('deporte_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Categorías -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Categorías <span class="text-red-500">*</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Selecciona una o más categorías para este torneo</p>

                    <div id="categorias-container" class="space-y-2 p-4 border border-gray-200 rounded-lg bg-gray-50">
                        <p class="text-sm text-gray-400 italic">Cargando categorías...</p>
                    </div>

                    @error('categorias')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('categorias.*')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Complejo -->
                <div>
                    <label for="complejo_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Complejo Deportivo <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="complejo_id"
                        id="complejo_id"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('complejo_id') border-red-500 @enderror"
                    >
                        <option value="">Selecciona un complejo</option>
                        @foreach($complejos as $complejo)
                            <option value="{{ $complejo->id }}" {{ old('complejo_id', $torneo->complejo_id) == $complejo->id ? 'selected' : '' }}>
                                {{ $complejo->nombre }}
                            </option>
                        @endforeach
                    </select>
                    @error('complejo_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Fechas -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Inicio <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            name="fecha_inicio"
                            id="fecha_inicio"
                            value="{{ old('fecha_inicio', $torneo->fecha_inicio->format('Y-m-d')) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('fecha_inicio') border-red-500 @enderror"
                        >
                        @error('fecha_inicio')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Finalización <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            name="fecha_fin"
                            id="fecha_fin"
                            value="{{ old('fecha_fin', $torneo->fecha_fin->format('Y-m-d')) }}"
                            required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('fecha_fin') border-red-500 @enderror"
                        >
                        @error('fecha_fin')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="fecha_limite_inscripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Límite Inscripción
                            <span class="text-gray-400 text-xs">(Opcional)</span>
                        </label>
                        <input
                            type="date"
                            name="fecha_limite_inscripcion"
                            id="fecha_limite_inscripcion"
                            value="{{ old('fecha_limite_inscripcion', $torneo->fecha_limite_inscripcion ? $torneo->fecha_limite_inscripcion->format('Y-m-d') : '') }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('fecha_limite_inscripcion') border-red-500 @enderror"
                        >
                        @error('fecha_limite_inscripcion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Descripción
                    </label>
                    <textarea
                        name="descripcion"
                        id="descripcion"
                        rows="4"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                        placeholder="Describe tu torneo, reglas especiales, premios, etc."
                    >{{ old('descripcion', $torneo->descripcion) }}</textarea>
                    @error('descripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Reglamento -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Reglamento
                        <span class="text-gray-400 text-xs">(Opcional)</span>
                    </label>
                    <p class="text-xs text-gray-500 mb-3">Podés escribir el reglamento o subir un PDF. Si subís un PDF, será lo que se muestre.</p>

                    {{-- Toggle Texto / PDF --}}
                    <div class="flex gap-1 bg-gray-100 rounded-lg p-1 mb-4 w-full sm:w-auto sm:inline-flex">
                        <button type="button" id="btn-modo-texto"
                            onclick="setModoReglamento('texto')"
                            class="flex-1 sm:flex-none px-4 py-2 rounded-md text-sm font-semibold transition">
                            Texto
                        </button>
                        <button type="button" id="btn-modo-pdf"
                            onclick="setModoReglamento('pdf')"
                            class="flex-1 sm:flex-none px-4 py-2 rounded-md text-sm font-semibold transition">
                            PDF
                        </button>
                    </div>

                    {{-- Modo Texto --}}
                    <div id="reglamento-texto-area">
                        <textarea
                            id="reglamento_texto"
                            name="reglamento_texto"
                            rows="6"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent text-sm @error('reglamento_texto') border-red-500 @enderror"
                            placeholder="Escribí aquí las reglas del torneo: modalidad de juego, criterios de desempate, normas de conducta, etc."
                        >{{ old('reglamento_texto', $torneo->reglamento_texto) }}</textarea>
                        @error('reglamento_texto')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Modo PDF --}}
                    <div id="reglamento-pdf-area" class="hidden">
                        {{-- PDF actual guardado --}}
                        @if($torneo->reglamento_pdf)
                            <div id="pdf-actual" class="flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg mb-3">
                                <svg class="w-8 h-8 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                                </svg>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-800">PDF actual</p>
                                    <a href="{{ asset('storage/' . $torneo->reglamento_pdf) }}" target="_blank"
                                        class="text-xs text-indigo-600 hover:underline">Ver PDF</a>
                                </div>
                                <button type="button" id="btn-quitar-pdf-actual"
                                    class="flex-shrink-0 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-100 rounded-full transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                            <input type="hidden" name="eliminar_reglamento_pdf" id="eliminar_reglamento_pdf" value="0">
                        @endif

                        {{-- Preview nuevo PDF seleccionado --}}
                        <div id="pdf-preview" class="hidden mb-3 flex items-center gap-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                            <svg class="w-8 h-8 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6zm-1 1.5L18.5 9H13V3.5zM6 20V4h5v7h7v9H6z"/>
                            </svg>
                            <div class="flex-1 min-w-0">
                                <p id="pdf-nombre" class="text-sm font-semibold text-gray-800 truncate"></p>
                                <p id="pdf-tamanio" class="text-xs text-gray-500"></p>
                            </div>
                            <button type="button" id="btn-quitar-pdf"
                                class="flex-shrink-0 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-100 rounded-full transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        {{-- Área de upload --}}
                        <div id="pdf-upload-area"
                            class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-indigo-400 transition cursor-pointer {{ $torneo->reglamento_pdf ? 'hidden' : '' }}"
                            onclick="document.getElementById('reglamento_pdf').click()">
                            <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <p class="text-sm font-semibold text-indigo-600 mb-1">Tocá para seleccionar un PDF</p>
                            <p class="text-xs text-gray-500">o arrastrá el archivo aquí</p>
                            <p class="text-xs text-gray-400 mt-2">Solo PDF · Máximo 20MB</p>
                            <input type="file" id="reglamento_pdf" name="reglamento_pdf"
                                accept="application/pdf" class="sr-only">
                        </div>
                        @error('reglamento_pdf')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Precio y Premios -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="precio_inscripcion" class="block text-sm font-medium text-gray-700 mb-2">
                            Precio de Inscripción
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-2.5 text-gray-500">$</span>
                            <input
                                type="number"
                                name="precio_inscripcion"
                                id="precio_inscripcion"
                                value="{{ old('precio_inscripcion', $torneo->precio_inscripcion) }}"
                                step="0.01"
                                min="0"
                                class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent no-spinners @error('precio_inscripcion') border-red-500 @enderror"
                                placeholder="0.00"
                            >
                        </div>
                        @error('precio_inscripcion')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="premios" class="block text-sm font-medium text-gray-700 mb-2">
                            Premios
                        </label>
                        <input
                            type="text"
                            name="premios"
                            id="premios"
                            value="{{ old('premios', $torneo->premios) }}"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('premios') border-red-500 @enderror"
                            placeholder="Ej: Trofeo + $10,000"
                        >
                        @error('premios')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Imagen de Banner -->
                <div>
                    <label for="imagen_banner" class="block text-sm font-medium text-gray-700 mb-2">
                        Imagen de Banner
                    </label>

                    @if($torneo->imagen_banner)
                        <div class="mb-4">
                            <img
                                src="{{ asset('storage/' . $torneo->imagen_banner) }}"
                                alt="Banner actual"
                                class="w-full h-48 object-cover rounded-lg"
                            >
                            <p class="mt-2 text-sm text-gray-500">Banner actual. Sube una nueva imagen para reemplazarlo.</p>
                        </div>
                    @endif

                    <input
                        type="file"
                        name="imagen_banner"
                        id="imagen_banner"
                        accept="image/*"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('imagen_banner') border-red-500 @enderror"
                    >
                    <p class="mt-1 text-sm text-gray-500">Formatos aceptados: JPG, PNG, GIF. Tamaño máximo: 10MB</p>
                    @error('imagen_banner')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Aviso de datos dependientes -->
        @if($tieneEquipos || $tieneGrupos || $tienePartidos)
            <div class="bg-amber-50 border-l-4 border-amber-400 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">¡Atención! Este torneo ya tiene datos configurados</h3>
                        <div class="mt-2 text-sm text-amber-700">
                            <p>El torneo actualmente tiene:</p>
                            <ul class="list-disc list-inside mt-1 space-y-1">
                                @if($tieneEquipos)
                                    <li><strong>{{ $torneo->equipos()->count() }} equipos</strong> cargados</li>
                                @endif
                                @if($tieneGrupos)
                                    <li><strong>{{ $torneo->grupos()->count() }} grupos</strong> configurados</li>
                                @endif
                                @if($tienePartidos)
                                    <li><strong>{{ $torneo->partidos()->count() }} partidos</strong> generados en el fixture</li>
                                @endif
                            </ul>
                            <p class="mt-3 font-semibold">
                                Si cambias el <strong>formato</strong>, <strong>número de grupos</strong> o <strong>tamaño de grupos</strong>,
                                se eliminarán automáticamente todos los grupos, partidos y la asignación de equipos a grupos.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Mensaje de advertencia si hay warning de sesión -->
        @if(session('warning'))
            <div id="warningBox" class="bg-red-50 border-l-4 border-red-400 p-4">
                <div class="flex">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 9.586 8.707 8.293z" clip-rule="evenodd"/>
                    </svg>
                    <div class="ml-3 flex-1">
                        <p class="text-sm text-red-700">{{ session('warning') }}</p>
                        <div class="mt-4">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    name="confirmar_cambios"
                                    id="confirmar_cambios"
                                    value="1"
                                    class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded"
                                >
                                <span class="ml-2 text-sm font-medium text-red-700">
                                    Entiendo y confirmo que quiero realizar estos cambios (se eliminarán los datos mencionados)
                                </span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Sección 2: Configuración del Formato -->
        <div class="bg-white shadow rounded-lg overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white">Configuración del Formato</h2>
            </div>
            <div class="p-6 space-y-6">
                <!-- Formato del Torneo -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-3">
                        Formato del Torneo <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3">
                        @foreach($formatos as $formato)
                            <label class="flex items-start p-4 border-2 rounded-lg cursor-pointer transition-all hover:border-indigo-500 formato-option {{ old('formato_id', $torneo->formato_id) == $formato->id ? 'border-indigo-500 bg-indigo-50' : 'border-gray-200' }}"
                                   data-tiene-grupos="{{ $formato->tiene_grupos ? 'true' : 'false' }}">
                                <input
                                    type="radio"
                                    name="formato_id"
                                    value="{{ $formato->id }}"
                                    {{ old('formato_id', $torneo->formato_id) == $formato->id ? 'checked' : '' }}
                                    class="formato-radio mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500"
                                    required
                                >
                                <div class="ml-3">
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

                <!-- Configuración de Cupos por Categoría (Liga/Eliminación Directa) -->
                <div id="configuracion-cupos" class="hidden space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
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
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold mr-2">
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
                                value="{{ old('categorias.'.$index.'.cupos_categoria', $categoria->pivot->cupos_categoria) }}"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 cupos-input @error('categorias.'.$index.'.cupos_categoria') border-red-500 @enderror"
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

                        <!-- Preview de partidos/rondas -->
                        <div class="mt-3 text-xs text-gray-600 bg-white p-2 rounded border border-gray-200">
                            💡 <strong>Ejemplo:</strong>
                            <span class="cupos-preview" data-index="{{ $index }}" data-formato="">
                                {{ $categoria->pivot->cupos_categoria ?? 8 }} equipos
                            </span>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Configuración de Grupos por Categoría (solo si tiene grupos) -->
                <div id="configuracion-grupos" class="hidden space-y-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
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
                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-600 text-white text-xs font-bold mr-2">
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
                                    value="{{ old('categorias.'.$index.'.numero_grupos', $categoria->pivot->numero_grupos) }}"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('categorias.'.$index.'.numero_grupos') border-red-500 @enderror"
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('categorias.'.$index.'.tamanio_grupo_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccionar</option>
                                    @foreach($tamanios as $tamanio)
                                        <option value="{{ $tamanio->id }}" {{ old('categorias.'.$index.'.tamanio_grupo_id', $categoria->pivot->tamanio_grupo_id) == $tamanio->id ? 'selected' : '' }}>
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
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 @error('categorias.'.$index.'.avance_grupos_id') border-red-500 @enderror"
                                >
                                    <option value="">Seleccionar</option>
                                    @foreach($avances as $avance)
                                        <option value="{{ $avance->id }}" {{ old('categorias.'.$index.'.avance_grupos_id', $categoria->pivot->avance_grupos_id) == $avance->id ? 'selected' : '' }}>
                                            {{ $avance->nombre }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categorias.'.$index.'.avance_grupos_id')
                                    <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Resumen de cupos para esta categoría -->
                        <div class="mt-3 text-xs text-gray-600 bg-white p-2 rounded border border-gray-200">
                            💡 <strong>Ejemplo:</strong>
                            @php
                                $numGrupos = $categoria->pivot->numero_grupos ?? 4;
                                $tamanioGrupo = $tamanios->find($categoria->pivot->tamanio_grupo_id);
                                $equiposPorGrupo = $tamanioGrupo ? $tamanioGrupo->tamanio : 2;
                                $cuposTotales = $numGrupos * $equiposPorGrupo;
                            @endphp
                            {{ $numGrupos }} grupos × {{ $equiposPorGrupo }} equipos = {{ $cuposTotales }} cupos totales para categoría {{ $categoria->nombre }}
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Botones de Acción -->
        <div class="flex flex-col sm:flex-row gap-4 sm:justify-end">
            <a
                href="{{ route('torneos.show', $torneo) }}"
                class="w-full sm:w-auto px-6 py-3 text-center border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors"
            >
                Cancelar
            </a>
            <button
                type="submit"
                class="w-full sm:w-auto px-6 py-3 bg-gradient-to-r from-indigo-500 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-600 hover:to-purple-700 transition-all shadow-md hover:shadow-lg"
            >
                Guardar Cambios
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Prevenir cambio de valor con la rueda del mouse en inputs numéricos
    const precioInput = document.getElementById('precio_inscripcion');

    if (precioInput) {
        precioInput.addEventListener('wheel', function(e) {
            e.preventDefault();
        }, { passive: false });
    }

    const formatoRadios = document.querySelectorAll('.formato-radio');
    const configuracionGrupos = document.getElementById('configuracion-grupos');
    const configuracionCupos = document.getElementById('configuracion-cupos');

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
                opt.classList.remove('border-indigo-500', 'bg-indigo-50');
                opt.classList.add('border-gray-200');
            });
            option.classList.remove('border-gray-200');
            option.classList.add('border-indigo-500', 'bg-indigo-50');

            // Mostrar/ocultar configuración según formato
            if (tieneGrupos) {
                configuracionGrupos.classList.remove('hidden');
                configuracionCupos.classList.add('hidden');
            } else {
                configuracionGrupos.classList.add('hidden');
                configuracionCupos.classList.remove('hidden');

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
                previewSpan.textContent = 'Ingrese cantidad de equipos';
            } else if (formato === 'ed') {
                previewSpan.textContent = 'Ingrese cantidad de equipos';
            } else {
                previewSpan.textContent = 'Ingrese cantidad de equipos';
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

    // Verificar si hay un formato preseleccionado
    const formatoChecked = document.querySelector('.formato-radio:checked');
    if (formatoChecked) {
        formatoChecked.dispatchEvent(new Event('change'));
    }

    // Validación de fechas
    const fechaInicio = document.getElementById('fecha_inicio');
    const fechaFin = document.getElementById('fecha_fin');

    fechaInicio.addEventListener('change', function() {
        fechaFin.min = this.value;
        if (fechaFin.value && fechaFin.value < this.value) {
            fechaFin.value = this.value;
        }
    });

    fechaFin.addEventListener('change', function() {
        if (this.value < fechaInicio.value) {
            alert('La fecha de finalización no puede ser anterior a la fecha de inicio');
            this.value = fechaInicio.value;
        }
    });

    // Manejar selección de deporte y mostrar categorías
    const deporteSelect = document.getElementById('deporte_id');
    const categoriasContainer = document.getElementById('categorias-container');

    // Categorías por deporte (desde el backend)
    const categoriasPorDeporte = @json($categoriasPorDeporte);

    // Categorías ya seleccionadas en el torneo
    const categoriasSeleccionadas = @json(old('categorias', $torneo->categorias->pluck('id')->toArray()));

    function cargarCategorias() {
        const deporteId = deporteSelect.value;

        if (!deporteId) {
            categoriasContainer.innerHTML = '<p class="text-sm text-gray-400 italic">Selecciona un deporte primero</p>';
            return;
        }

        const categorias = categoriasPorDeporte[deporteId] || [];

        if (categorias.length === 0) {
            categoriasContainer.innerHTML = '<p class="text-sm text-gray-400 italic">No hay categorías disponibles para este deporte</p>';
            return;
        }

        // Generar checkboxes para cada categoría
        let html = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">';

        categorias.forEach(categoria => {
            const checked = categoriasSeleccionadas.includes(categoria.id) ? 'checked' : '';
            html += `
                <label class="flex items-center space-x-2 p-2 border border-gray-200 rounded hover:bg-indigo-50 cursor-pointer transition">
                    <input
                        type="checkbox"
                        name="categorias[]"
                        value="${categoria.id}"
                        ${checked}
                        class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                    >
                    <span class="text-sm font-medium text-gray-700">${categoria.nombre}</span>
                </label>
            `;
        });

        html += '</div>';
        categoriasContainer.innerHTML = html;
    }

    deporteSelect.addEventListener('change', cargarCategorias);

    // Cargar categorías al inicio
    cargarCategorias();

    // ── Reglamento: toggle texto/PDF ──────────────────────────────────
    window.setModoReglamento = function(modo) {
        const btnTexto  = document.getElementById('btn-modo-texto');
        const btnPdf    = document.getElementById('btn-modo-pdf');
        const areaTexto = document.getElementById('reglamento-texto-area');
        const areaPdf   = document.getElementById('reglamento-pdf-area');
        const activeClasses   = ['bg-white', 'text-indigo-700', 'shadow'];
        const inactiveClasses = ['text-gray-500'];

        if (modo === 'texto') {
            btnTexto.classList.add(...activeClasses);
            btnTexto.classList.remove(...inactiveClasses);
            btnPdf.classList.remove(...activeClasses);
            btnPdf.classList.add(...inactiveClasses);
            areaTexto.classList.remove('hidden');
            areaPdf.classList.add('hidden');
        } else {
            btnPdf.classList.add(...activeClasses);
            btnPdf.classList.remove(...inactiveClasses);
            btnTexto.classList.remove(...activeClasses);
            btnTexto.classList.add(...inactiveClasses);
            areaPdf.classList.remove('hidden');
            areaTexto.classList.add('hidden');
        }
    };

    // Inicializar modo según datos guardados
    const tieneReglamentoPdf = @json((bool) $torneo->reglamento_pdf);
    setModoReglamento(tieneReglamentoPdf ? 'pdf' : 'texto');

    // Botón quitar PDF actual (guardado)
    const btnQuitarPdfActual = document.getElementById('btn-quitar-pdf-actual');
    if (btnQuitarPdfActual) {
        btnQuitarPdfActual.addEventListener('click', function() {
            document.getElementById('pdf-actual').classList.add('hidden');
            document.getElementById('pdf-upload-area').classList.remove('hidden');
            document.getElementById('eliminar_reglamento_pdf').value = '1';
        });
    }

    // PDF upload preview
    const pdfInput      = document.getElementById('reglamento_pdf');
    const pdfPreview    = document.getElementById('pdf-preview');
    const pdfUploadArea = document.getElementById('pdf-upload-area');
    const pdfNombre     = document.getElementById('pdf-nombre');
    const pdfTamanio    = document.getElementById('pdf-tamanio');
    const btnQuitarPdf  = document.getElementById('btn-quitar-pdf');

    if (pdfInput) {
        const pdfDropArea = document.getElementById('pdf-upload-area');
        ['dragenter', 'dragover'].forEach(e => pdfDropArea.addEventListener(e, ev => { ev.preventDefault(); pdfDropArea.classList.add('border-indigo-500', 'bg-indigo-50'); }));
        ['dragleave', 'drop'].forEach(e => pdfDropArea.addEventListener(e, ev => { ev.preventDefault(); pdfDropArea.classList.remove('border-indigo-500', 'bg-indigo-50'); }));
        pdfDropArea.addEventListener('drop', ev => {
            const file = ev.dataTransfer.files[0];
            if (file && file.type === 'application/pdf') { mostrarPdfPreview(file); }
            else { alert('Solo se aceptan archivos PDF.'); }
        });

        pdfInput.addEventListener('change', function() {
            if (this.files[0]) { mostrarPdfPreview(this.files[0]); }
        });

        btnQuitarPdf.addEventListener('click', function() {
            pdfInput.value = '';
            pdfPreview.classList.add('hidden');
            pdfUploadArea.classList.remove('hidden');
        });
    }

    function mostrarPdfPreview(file) {
        if (file.size > 20971520) { alert('El PDF no debe superar los 20MB.'); return; }
        pdfNombre.textContent = file.name;
        pdfTamanio.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
        pdfPreview.classList.remove('hidden');
        pdfUploadArea.classList.add('hidden');
        const dt = new DataTransfer();
        dt.items.add(file);
        pdfInput.files = dt.files;
    }

    // Control de confirmación de cambios destructivos
    const warningBox = document.getElementById('warningBox');
    const confirmarCheckbox = document.getElementById('confirmar_cambios');
    const submitButton = document.querySelector('button[type="submit"]');

    if (warningBox && confirmarCheckbox && submitButton) {
        // Deshabilitar el botón inicialmente si hay warning
        submitButton.disabled = true;
        submitButton.classList.add('opacity-50', 'cursor-not-allowed');

        // Habilitar/deshabilitar según el checkbox
        confirmarCheckbox.addEventListener('change', function() {
            if (this.checked) {
                submitButton.disabled = false;
                submitButton.classList.remove('opacity-50', 'cursor-not-allowed');
            } else {
                submitButton.disabled = true;
                submitButton.classList.add('opacity-50', 'cursor-not-allowed');
            }
        });
    }
});
</script>
@endsection
