@extends('layouts.dashboard')

@section('title', 'Crear Torneo - Paso 1')
@section('page-title', 'Crear Nuevo Torneo')

@section('content')
<div class="max-w-4xl mx-auto">
    <!-- Progress Bar -->
    <div class="mb-6 sm:mb-8">
        <div class="flex items-center justify-between mb-2">
            <span class="text-sm font-medium text-brand-600">Paso 1 de 2</span>
            <span class="text-sm text-gray-500">Información General</span>
        </div>
        <div class="w-full bg-gray-200 rounded-full h-2">
            <div class="bg-brand-600 h-2 rounded-full" style="width: 50%"></div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 md:p-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-6">Información General del Torneo</h2>

        <form action="{{ route('torneos.store-step1') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- Nombre del Torneo -->
            <div class="mb-6">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre del Torneo <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre') }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('nombre') border-red-500 @enderror"
                    placeholder="Ej: Torneo de Padel Primavera 2025"
                >
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Deporte -->
            <div class="mb-6">
                <label for="deporte_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Deporte <span class="text-red-500">*</span>
                </label>
                <select
                    id="deporte_id"
                    name="deporte_id"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('deporte_id') border-red-500 @enderror"
                >
                    <option value="">Seleccionar deporte</option>
                    @foreach($deportes as $deporte)
                        <option value="{{ $deporte->id }}" {{ old('deporte_id') == $deporte->id ? 'selected' : '' }}>
                            {{ $deporte->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('deporte_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Categorías -->
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Categorías <span class="text-red-500">*</span>
                </label>
                <p class="text-xs text-gray-500 mb-3">Selecciona una o más categorías para este torneo (máximo 10)</p>

                <div id="categorias-container" class="space-y-2 p-4 border border-gray-200 rounded-lg bg-gray-50">
                    <p class="text-sm text-gray-400 italic">Selecciona un deporte primero</p>
                </div>

                <p id="categorias-counter" class="mt-2 text-xs text-gray-600 hidden">
                    Categorías seleccionadas: <span id="categorias-count">0</span>/10
                </p>

                @error('categorias')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @error('categorias.*')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Complejo -->
            <div class="mb-6">
                <label for="complejo_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Complejo Deportivo <span class="text-red-500">*</span>
                </label>
                <select
                    id="complejo_id"
                    name="complejo_id"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('complejo_id') border-red-500 @enderror"
                >
                    <option value="">Seleccionar complejo</option>
                    @foreach($complejos as $complejo)
                        <option value="{{ $complejo->id }}" {{ old('complejo_id') == $complejo->id ? 'selected' : '' }}>
                            {{ $complejo->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('complejo_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                @if($complejos->count() === 0)
                    <p class="mt-1 text-sm text-accent-600">
                        No tienes complejos creados.
                        <a href="{{ route('complejos.create') }}" class="underline hover:text-accent-700">Crear complejo</a>
                    </p>
                @endif
            </div>

            <!-- Fechas -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-6">
                <div>
                    <label for="fecha_inicio" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha Inicio <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="fecha_inicio"
                        name="fecha_inicio"
                        value="{{ old('fecha_inicio') }}"
                        required
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('fecha_inicio') border-red-500 @enderror"
                    >
                    @error('fecha_inicio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="fecha_fin" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha Fin <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        id="fecha_fin"
                        name="fecha_fin"
                        value="{{ old('fecha_fin') }}"
                        required
                        min="{{ date('Y-m-d') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('fecha_fin') border-red-500 @enderror"
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
                        id="fecha_limite_inscripcion"
                        name="fecha_limite_inscripcion"
                        value="{{ old('fecha_limite_inscripcion') }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('fecha_limite_inscripcion') border-red-500 @enderror"
                    >
                    @error('fecha_limite_inscripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Descripción -->
            <div class="mb-6">
                <label for="descripcion" class="block text-sm font-medium text-gray-700 mb-2">
                    Descripción
                    <span class="text-gray-400 text-xs">(Opcional)</span>
                </label>
                <textarea
                    id="descripcion"
                    name="descripcion"
                    rows="4"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('descripcion') border-red-500 @enderror"
                    placeholder="Describe tu torneo, reglas especiales, información importante..."
                >{{ old('descripcion') }}</textarea>
                @error('descripcion')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Reglamento -->
            <div class="mb-6">
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
                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent text-sm @error('reglamento_texto') border-red-500 @enderror"
                        placeholder="Escribí aquí las reglas del torneo: modalidad de juego, criterios de desempate, normas de conducta, etc."
                    >{{ old('reglamento_texto') }}</textarea>
                    @error('reglamento_texto')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Modo PDF --}}
                <div id="reglamento-pdf-area" class="hidden">
                    {{-- Preview PDF seleccionado --}}
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
                        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-brand-400 transition cursor-pointer"
                        onclick="document.getElementById('reglamento_pdf').click()">
                        <svg class="mx-auto h-10 w-10 text-gray-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <p class="text-sm font-semibold text-brand-600 mb-1">Tocá para seleccionar un PDF</p>
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
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6">
                <div>
                    <label for="precio_inscripcion" class="block text-sm font-medium text-gray-700 mb-2">
                        Precio Inscripción
                        <span class="text-gray-400 text-xs">(Opcional)</span>
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-2 text-gray-500">$</span>
                        <input
                            type="number"
                            id="precio_inscripcion"
                            name="precio_inscripcion"
                            value="{{ old('precio_inscripcion') }}"
                            step="0.01"
                            min="0"
                            class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent no-spinners @error('precio_inscripcion') border-red-500 @enderror"
                            placeholder="0.00"
                        >
                    </div>
                    @error('precio_inscripcion')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Dejar vacío si es gratuito</p>
                </div>

                <div>
                    <label for="premios" class="block text-sm font-medium text-gray-700 mb-2">
                        Premios
                        <span class="text-gray-400 text-xs">(Opcional)</span>
                    </label>
                    <textarea
                        id="premios"
                        name="premios"
                        rows="3"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('premios') border-red-500 @enderror"
                        placeholder="Ej: 1er puesto: $10,000&#10;2do puesto: $5,000&#10;3er puesto: $2,000"
                    >{{ old('premios') }}</textarea>
                    @error('premios')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Imagen Banner -->
            <div class="mb-6">
                <label for="imagen_banner" class="block text-sm font-medium text-gray-700 mb-2">
                    Imagen Banner
                    <span class="text-gray-400 text-xs">(Opcional)</span>
                </label>

                <!-- Preview de imagen -->
                <div id="image-preview" class="hidden mb-4">
                    <div class="relative inline-block">
                        <img id="preview-img" src="" alt="Preview" class="max-w-full h-48 rounded-lg border-2 border-brand-400">
                        <button type="button" id="remove-image" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mt-2" id="image-name"></p>
                </div>

                <!-- Upload area -->
                <div id="upload-area" class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-lg hover:border-brand-400 transition">
                    <div class="space-y-1 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="flex text-sm text-gray-600 justify-center">
                            <label for="imagen_banner" class="relative cursor-pointer bg-white rounded-md font-medium text-brand-600 hover:text-brand-500 focus-within:outline-none">
                                <span>Subir imagen</span>
                                <input id="imagen_banner" name="imagen_banner" type="file" accept="image/*" class="sr-only">
                            </label>
                            <p class="pl-1">o arrastra y suelta</p>
                        </div>
                        <p class="text-xs text-gray-500">PNG, JPG hasta 10MB</p>
                    </div>
                </div>
                @error('imagen_banner')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('torneos.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-center text-sm sm:text-base order-2 sm:order-1">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base order-1 sm:order-2">
                    Siguiente: Formato del Torneo
                    <svg class="w-4 h-4 inline ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Prevenir cambio de valor con la rueda del mouse en inputs numéricos
document.addEventListener('DOMContentLoaded', function() {
    const precioInput = document.getElementById('precio_inscripcion');

    if (precioInput) {
        precioInput.addEventListener('wheel', function(e) {
            e.preventDefault();
        }, { passive: false });

        // Alternativa: remover el foco cuando se intenta usar la rueda
        precioInput.addEventListener('focus', function() {
            this.addEventListener('wheel', function(e) {
                e.preventDefault();
                this.blur();
                setTimeout(() => this.focus(), 0);
            }, { passive: false });
        });
    }
});

// Preview de imagen
const imagenBanner = document.getElementById('imagen_banner');
const uploadArea = document.getElementById('upload-area');
const imagePreview = document.getElementById('image-preview');
const previewImg = document.getElementById('preview-img');
const imageName = document.getElementById('image-name');
const removeImageBtn = document.getElementById('remove-image');

imagenBanner.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validar tamaño (10MB = 10485760 bytes)
        if (file.size > 10485760) {
            alert('La imagen no debe superar los 10MB');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            imageName.textContent = file.name;
            uploadArea.classList.add('hidden');
            imagePreview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    }
});

removeImageBtn.addEventListener('click', function() {
    imagenBanner.value = '';
    previewImg.src = '';
    imageName.textContent = '';
    imagePreview.classList.add('hidden');
    uploadArea.classList.remove('hidden');
});

// Validación de fechas
document.getElementById('fecha_inicio').addEventListener('change', function() {
    const fechaInicio = this.value;
    document.getElementById('fecha_fin').setAttribute('min', fechaInicio);
    document.getElementById('fecha_limite_inscripcion').setAttribute('max', fechaInicio);
});

// Manejar selección de deporte y mostrar categorías
const deporteSelect = document.getElementById('deporte_id');
const categoriasContainer = document.getElementById('categorias-container');
const categoriasCounter = document.getElementById('categorias-counter');
const categoriasCount = document.getElementById('categorias-count');
const MAX_CATEGORIAS = 10;

// Categorías por deporte (desde el backend)
const categoriasPorDeporte = @json($categoriasPorDeporte);

function actualizarContador() {
    const checkboxes = document.querySelectorAll('input[name="categorias[]"]');
    const seleccionados = Array.from(checkboxes).filter(cb => cb.checked).length;

    categoriasCount.textContent = seleccionados;

    if (seleccionados > 0) {
        categoriasCounter.classList.remove('hidden');

        // Cambiar color si se alcanza el límite
        if (seleccionados >= MAX_CATEGORIAS) {
            categoriasCounter.classList.remove('text-gray-600');
            categoriasCounter.classList.add('text-red-600', 'font-semibold');
        } else {
            categoriasCounter.classList.remove('text-red-600', 'font-semibold');
            categoriasCounter.classList.add('text-gray-600');
        }
    } else {
        categoriasCounter.classList.add('hidden');
    }

    // Deshabilitar checkboxes no seleccionados si se alcanza el límite
    if (seleccionados >= MAX_CATEGORIAS) {
        checkboxes.forEach(cb => {
            if (!cb.checked) {
                cb.disabled = true;
                cb.closest('label').classList.add('opacity-50', 'cursor-not-allowed');
                cb.closest('label').classList.remove('hover:bg-brand-50', 'cursor-pointer');
            }
        });
    } else {
        checkboxes.forEach(cb => {
            cb.disabled = false;
            cb.closest('label').classList.remove('opacity-50', 'cursor-not-allowed');
            cb.closest('label').classList.add('hover:bg-brand-50', 'cursor-pointer');
        });
    }
}

deporteSelect.addEventListener('change', function() {
    const deporteId = this.value;

    if (!deporteId) {
        categoriasContainer.innerHTML = '<p class="text-sm text-gray-400 italic">Selecciona un deporte primero</p>';
        categoriasCounter.classList.add('hidden');
        return;
    }

    const categorias = categoriasPorDeporte[deporteId] || [];

    if (categorias.length === 0) {
        categoriasContainer.innerHTML = '<p class="text-sm text-gray-400 italic">No hay categorías disponibles para este deporte</p>';
        categoriasCounter.classList.add('hidden');
        return;
    }

    // Generar checkboxes para cada categoría
    let html = '<div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-3">';

    categorias.forEach(categoria => {
        const checked = @json(old('categorias', [])).includes(categoria.id.toString()) ? 'checked' : '';
        html += `
            <label class="flex items-center space-x-2 p-2 border border-gray-200 rounded hover:bg-brand-50 cursor-pointer transition">
                <input
                    type="checkbox"
                    name="categorias[]"
                    value="${categoria.id}"
                    ${checked}
                    class="w-4 h-4 text-brand-600 border-gray-300 rounded focus:ring-brand-500 categoria-checkbox"
                >
                <span class="text-sm font-medium text-gray-700">${categoria.nombre}</span>
            </label>
        `;
    });

    html += '</div>';
    categoriasContainer.innerHTML = html;

    // Agregar event listeners a los checkboxes
    document.querySelectorAll('.categoria-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', actualizarContador);
    });

    // Actualizar contador inicial
    actualizarContador();
});

// Trigger al cargar si hay un deporte seleccionado (old input)
if (deporteSelect.value) {
    deporteSelect.dispatchEvent(new Event('change'));
}

// ── Reglamento: toggle texto/PDF ──────────────────────────────────
function setModoReglamento(modo) {
    const btnTexto   = document.getElementById('btn-modo-texto');
    const btnPdf     = document.getElementById('btn-modo-pdf');
    const areaTexto  = document.getElementById('reglamento-texto-area');
    const areaPdf    = document.getElementById('reglamento-pdf-area');
    const activeClasses  = ['bg-white', 'text-brand-700', 'shadow'];
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
}

// Inicializar modo según old input
setModoReglamento(@json(old('reglamento_pdf') ? 'pdf' : 'texto'));

// PDF upload preview
const pdfInput       = document.getElementById('reglamento_pdf');
const pdfPreview     = document.getElementById('pdf-preview');
const pdfUploadArea  = document.getElementById('pdf-upload-area');
const pdfNombre      = document.getElementById('pdf-nombre');
const pdfTamanio     = document.getElementById('pdf-tamanio');
const btnQuitarPdf   = document.getElementById('btn-quitar-pdf');

// Drag & drop
const pdfDropArea = document.getElementById('pdf-upload-area');
['dragenter', 'dragover'].forEach(e => pdfDropArea.addEventListener(e, ev => { ev.preventDefault(); pdfDropArea.classList.add('border-brand-500', 'bg-brand-50'); }));
['dragleave', 'drop'].forEach(e => pdfDropArea.addEventListener(e, ev => { ev.preventDefault(); pdfDropArea.classList.remove('border-brand-500', 'bg-brand-50'); }));
pdfDropArea.addEventListener('drop', ev => {
    const file = ev.dataTransfer.files[0];
    if (file && file.type === 'application/pdf') { mostrarPdfPreview(file); }
    else { alert('Solo se aceptan archivos PDF.'); }
});

pdfInput.addEventListener('change', function() {
    if (this.files[0]) { mostrarPdfPreview(this.files[0]); }
});

function mostrarPdfPreview(file) {
    if (file.size > 20971520) { alert('El PDF no debe superar los 20MB.'); return; }
    pdfNombre.textContent = file.name;
    pdfTamanio.textContent = (file.size / 1024 / 1024).toFixed(2) + ' MB';
    pdfPreview.classList.remove('hidden');
    pdfUploadArea.classList.add('hidden');
    // Sincronizar el input si vino de drag&drop
    const dt = new DataTransfer();
    dt.items.add(file);
    pdfInput.files = dt.files;
}

btnQuitarPdf.addEventListener('click', function() {
    pdfInput.value = '';
    pdfPreview.classList.add('hidden');
    pdfUploadArea.classList.remove('hidden');
});
</script>
@endsection
