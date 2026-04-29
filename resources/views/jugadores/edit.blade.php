@extends('layouts.dashboard')

@section('title', 'Editar Jugador')
@section('page-title', 'Editar Jugador')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600">
            <li>
                <a href="{{ route('jugadores.index') }}" class="hover:text-brand-600">Jugadores</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Editar Jugador</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 md:p-8">
        <form action="{{ route('jugadores.update', $jugador) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nombre -->
                <div>
                    <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        value="{{ old('nombre', $jugador->nombre) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('nombre') border-red-500 @enderror"
                        placeholder="Ej: Juan"
                    >
                    @error('nombre')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Apellido -->
                <div>
                    <label for="apellido" class="block text-sm font-medium text-gray-700 mb-2">
                        Apellido <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="apellido"
                        name="apellido"
                        value="{{ old('apellido', $jugador->apellido) }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('apellido') border-red-500 @enderror"
                        placeholder="Ej: P�rez"
                    >
                    @error('apellido')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- DNI -->
                <div>
                    <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">
                        DNI
                    </label>
                    <input
                        type="text"
                        id="dni"
                        name="dni"
                        value="{{ old('dni', $jugador->dni) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('dni') border-red-500 @enderror"
                        placeholder="12345678"
                    >
                    @error('dni')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tel�fono -->
                <div>
                    <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                        Tel�fono
                    </label>
                    <input
                        type="text"
                        id="telefono"
                        name="telefono"
                        value="{{ old('telefono', $jugador->telefono) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('telefono') border-red-500 @enderror"
                        placeholder="3416123456"
                    >
                    @error('telefono')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Fecha de Nacimiento y Género -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Fecha de Nacimiento -->
                <div>
                    <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-2">
                        Fecha de Nacimiento
                    </label>
                    <input
                        type="text"
                        id="fecha_nacimiento"
                        name="fecha_nacimiento"
                        value="{{ old('fecha_nacimiento', $jugador->fecha_nacimiento ? $jugador->fecha_nacimiento->format('d/m/Y') : '') }}"
                        inputmode="numeric"
                        maxlength="10"
                        placeholder="DD/MM/AAAA"
                        autocomplete="off"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('fecha_nacimiento') border-red-500 @enderror"
                    >
                    @error('fecha_nacimiento')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Género -->
                <div>
                    <label for="genero" class="block text-sm font-medium text-gray-700 mb-2">
                        Género
                    </label>
                    <div class="flex flex-wrap gap-4 pt-1">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="genero" value="masculino" {{ old('genero', $jugador->genero) == 'masculino' ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-700">Masculino</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="genero" value="femenino" {{ old('genero', $jugador->genero) == 'femenino' ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-700">Femenino</span>
                        </label>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="radio" name="genero" value="otro" {{ old('genero', $jugador->genero) == 'otro' ? 'checked' : '' }} class="text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-gray-700">Otro</span>
                        </label>
                    </div>
                    @error('genero')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Ranking y Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                <!-- Ranking -->
                <div>
                    <label for="ranking" class="block text-sm font-medium text-gray-700 mb-2">
                        Ranking
                    </label>
                    <input
                        type="text"
                        id="ranking"
                        name="ranking"
                        value="{{ old('ranking', $jugador->ranking) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('ranking') border-red-500 @enderror"
                        placeholder="Ej: 1234, 3.5, A+"
                    >
                    @error('ranking')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                        Email
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email', $jugador->email) }}"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('email') border-red-500 @enderror"
                        placeholder="jugador@example.com"
                    >
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500 flex items-center gap-1">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Este email se utilizar&aacute; para enviar notificaciones al jugador
                    </p>
                </div>
            </div>

            <!-- Foto -->
            <div class="mt-6">
                <label for="foto" class="block text-sm font-medium text-gray-700 mb-2">
                    Foto
                </label>

                @if($jugador->foto)
                    <div class="mb-3" id="current-photo">
                        <div class="relative inline-block">
                            <img src="{{ asset('storage/' . $jugador->foto) }}" alt="{{ $jugador->nombre_completo }}" class="w-32 h-32 rounded-full object-cover border-4 border-gray-300">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Foto actual</p>
                    </div>
                @endif

                <!-- Preview de nueva imagen -->
                <div id="image-preview" class="hidden mb-4">
                    <div class="relative inline-block">
                        <img id="preview-img" src="" alt="Preview" class="w-32 h-32 rounded-full object-cover border-4 border-brand-400">
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
                            <label for="foto" class="relative cursor-pointer bg-white rounded-md font-medium text-brand-600 hover:text-brand-500 focus-within:outline-none">
                                <span>Cambiar foto</span>
                                <input id="foto" name="foto" type="file" accept="image/*" class="sr-only">
                            </label>
                            <p class="pl-1">o arrastra y suelta</p>
                        </div>
                        <p class="text-xs text-gray-500">JPG, PNG, GIF hasta 2MB</p>
                    </div>
                </div>
                @error('foto')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Advertencia si est� en uso -->
            @if($jugador->inscripciones()->count() > 0 || $jugador->equipos()->count() > 0)
                <div class="mt-6 p-4 bg-accent-50 border border-accent-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-accent-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-sm text-accent-800">
                            <p class="font-semibold mb-1">Jugador en uso</p>
                            <p>Este jugador est� inscrito en torneos o forma parte de equipos. Los cambios afectar�n estas inscripciones.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-6 mt-6 border-t border-gray-200">
                <a href="{{ route('jugadores.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-center text-sm sm:text-base order-2 sm:order-1">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base order-1 sm:order-2">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Preview de imagen
const foto = document.getElementById('foto');
const uploadArea = document.getElementById('upload-area');
const imagePreview = document.getElementById('image-preview');
const previewImg = document.getElementById('preview-img');
const imageName = document.getElementById('image-name');
const removeImageBtn = document.getElementById('remove-image');
const currentPhoto = document.getElementById('current-photo');

foto.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Validar tamaño (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            alert('La foto no debe superar los 2MB');
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            imageName.textContent = file.name;
            uploadArea.classList.add('hidden');
            imagePreview.classList.remove('hidden');
            if (currentPhoto) {
                currentPhoto.classList.add('hidden');
            }
        };
        reader.readAsDataURL(file);
    }
});

removeImageBtn.addEventListener('click', function() {
    foto.value = '';
    previewImg.src = '';
    imageName.textContent = '';
    imagePreview.classList.add('hidden');
    uploadArea.classList.remove('hidden');
    if (currentPhoto) {
        currentPhoto.classList.remove('hidden');
    }
});

// Máscara DD/MM/AAAA
document.getElementById('fecha_nacimiento').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 3 && value.length <= 4) {
        value = value.slice(0, 2) + '/' + value.slice(2);
    } else if (value.length >= 5) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4) + '/' + value.slice(4, 8);
    }
    e.target.value = value;
});
</script>
@endsection
