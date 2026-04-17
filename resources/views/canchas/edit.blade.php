@extends('layouts.dashboard')

@section('title', 'Editar Cancha')
@section('page-title', 'Editar Cancha')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600">
            <li>
                <a href="{{ route('complejos.index') }}" class="hover:text-indigo-600">Complejos</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li>
                <a href="{{ route('complejos.canchas.index', $complejo) }}" class="hover:text-indigo-600">{{ $complejo->nombre }}</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Editar: {{ $cancha->nombre }}</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 md:p-8">
        <!-- Info del complejo -->
        <div class="mb-6 pb-6 border-b border-gray-200">
            <p class="text-xs sm:text-sm text-gray-600">Cancha de:</p>
            <p class="text-lg font-semibold text-gray-900">{{ $complejo->nombre }}</p>
        </div>

        <form action="{{ route('complejos.canchas.update', [$complejo, $cancha]) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Nombre de la Cancha -->
            <div class="mb-6">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre de la Cancha <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre', $cancha->nombre) }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nombre') border-red-500 @enderror"
                    placeholder="Ej: Cancha Principal"
                >
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Nombre descriptivo para identificar la cancha</p>
            </div>

            <!-- Número de Cancha -->
            <div class="mb-6">
                <label for="numero" class="block text-sm font-medium text-gray-700 mb-2">
                    Número de Cancha <span class="text-red-500">*</span>
                </label>
                <input
                    type="number"
                    id="numero"
                    name="numero"
                    value="{{ old('numero', $cancha->numero) }}"
                    required
                    min="1"
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('numero') border-red-500 @enderror"
                    placeholder="1"
                >
                @error('numero')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">Número para identificar la cancha (ej: 1, 2, 3...)</p>
            </div>

            <!-- Botones -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('complejos.canchas.index', $complejo) }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-center text-sm sm:text-base">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
