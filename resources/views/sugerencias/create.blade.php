@extends('layouts.dashboard')

@section('title', 'Enviar Sugerencia')
@section('page-title', 'Enviar Sugerencia')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-brand-500 to-purple-600">
            <h2 class="text-xl font-semibold text-white flex items-center">
                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                </svg>
                Comparte tu feedback con nosotros
            </h2>
            <p class="text-brand-100 text-sm mt-1">Nos ayudas a mejorar la plataforma</p>
        </div>

        <!-- Formulario -->
        <form action="{{ route('sugerencias.store') }}" method="POST" class="p-6 space-y-6">
            @csrf

            <!-- Tipo de sugerencia -->
            <div>
                <label for="tipo" class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de mensaje
                    <span class="text-red-500">*</span>
                </label>
                <select name="tipo" id="tipo" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('tipo') border-red-500 @enderror">
                    <option value="">Selecciona un tipo</option>
                    <option value="sugerencia" {{ old('tipo') === 'sugerencia' ? 'selected' : '' }}>
                        Sugerencia - Tengo una idea para mejorar la plataforma
                    </option>
                    <option value="soporte" {{ old('tipo') === 'soporte' ? 'selected' : '' }}>
                        Soporte - Necesito ayuda con algo
                    </option>
                    <option value="bug" {{ old('tipo') === 'bug' ? 'selected' : '' }}>
                        Bug - Encontré un error o problema técnico
                    </option>
                    <option value="otro" {{ old('tipo') === 'otro' ? 'selected' : '' }}>
                        Otro - Otro tipo de consulta
                    </option>
                </select>
                @error('tipo')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror

                <!-- Descripción dinámica del tipo seleccionado -->
                <div id="tipo-descripcion" class="mt-2 text-sm text-gray-600 bg-gray-50 p-3 rounded hidden">
                    <div data-tipo="sugerencia" class="tipo-info hidden">
                        <strong>Sugerencia:</strong> Comparte ideas de nuevas funcionalidades, mejoras en el diseño o cambios que te gustaría ver.
                    </div>
                    <div data-tipo="soporte" class="tipo-info hidden">
                        <strong>Soporte:</strong> ¿Tienes dudas sobre cómo usar alguna funcionalidad? Estamos aquí para ayudarte.
                    </div>
                    <div data-tipo="bug" class="tipo-info hidden">
                        <strong>Bug:</strong> Si algo no funciona como debería, cuéntanos los detalles para resolverlo rápidamente.
                    </div>
                    <div data-tipo="otro" class="tipo-info hidden">
                        <strong>Otro:</strong> Cualquier otro tipo de consulta, comentario o feedback que quieras compartir.
                    </div>
                </div>
            </div>

            <!-- Asunto -->
            <div>
                <label for="asunto" class="block text-sm font-medium text-gray-700 mb-2">
                    Asunto
                    <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    name="asunto"
                    id="asunto"
                    value="{{ old('asunto') }}"
                    maxlength="255"
                    required
                    placeholder="Ejemplo: Agregar opción de exportar fixture a PDF"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('asunto') border-red-500 @enderror"
                >
                @error('asunto')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Máximo 255 caracteres</p>
            </div>

            <!-- Mensaje -->
            <div>
                <label for="mensaje" class="block text-sm font-medium text-gray-700 mb-2">
                    Mensaje
                    <span class="text-red-500">*</span>
                </label>
                <textarea
                    name="mensaje"
                    id="mensaje"
                    rows="6"
                    required
                    minlength="10"
                    placeholder="Describe tu sugerencia, problema o consulta con el mayor detalle posible..."
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent @error('mensaje') border-red-500 @enderror"
                >{{ old('mensaje') }}</textarea>
                @error('mensaje')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Mínimo 10 caracteres - Sé lo más específico posible para ayudarnos a entender mejor</p>
            </div>

            <!-- Info box -->
            <div class="bg-blue-50 border-l-4 border-blue-500 p-4 rounded">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Ten en cuenta:</strong> Revisamos todos los mensajes y nos ponemos en contacto lo antes posible. Los tiempos de respuesta pueden variar según la complejidad de la consulta.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-gray-200">
                <a href="{{ route('sugerencias.index') }}" class="inline-flex justify-center items-center px-6 py-2.5 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200 text-sm sm:text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Cancelar
                </a>
                <button type="submit" class="flex-1 inline-flex justify-center items-center px-6 py-2.5 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                    </svg>
                    Enviar Sugerencia
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
    // Mostrar descripción dinámica según el tipo seleccionado
    document.getElementById('tipo').addEventListener('change', function() {
        const selectedTipo = this.value;
        const descripcionContainer = document.getElementById('tipo-descripcion');
        const allInfos = document.querySelectorAll('.tipo-info');

        // Ocultar todas las descripciones
        allInfos.forEach(info => info.classList.add('hidden'));

        if (selectedTipo) {
            // Mostrar el contenedor
            descripcionContainer.classList.remove('hidden');

            // Mostrar la descripción correspondiente
            const selectedInfo = document.querySelector(`[data-tipo="${selectedTipo}"]`);
            if (selectedInfo) {
                selectedInfo.classList.remove('hidden');
            }
        } else {
            // Ocultar el contenedor si no hay selección
            descripcionContainer.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
