@extends('layouts.dashboard')

@section('title', 'Editar Categoría')
@section('page-title', 'Editar Categoría')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600">
            <li>
                <a href="{{ route('categorias.index') }}" class="hover:text-indigo-600">Categorías</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Editar Categoría</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 md:p-8">
        <form action="{{ route('categorias.update', $categoria) }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Deporte -->
            <div class="mb-6">
                <label for="deporte_id" class="block text-sm font-medium text-gray-700 mb-2">
                    Deporte <span class="text-red-500">*</span>
                </label>
                <select
                    id="deporte_id"
                    name="deporte_id"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('deporte_id') border-red-500 @enderror"
                >
                    <option value="">Seleccionar deporte</option>
                    @foreach($deportes as $deporte)
                        <option value="{{ $deporte->id }}" {{ old('deporte_id', $categoria->deporte_id) == $deporte->id ? 'selected' : '' }}>
                            {{ $deporte->nombre }}
                        </option>
                    @endforeach
                </select>
                @error('deporte_id')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nombre de la Categoría -->
            <div class="mb-6">
                <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                    Nombre de la Categoría <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="nombre"
                    name="nombre"
                    value="{{ old('nombre', $categoria->nombre) }}"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('nombre') border-red-500 @enderror"
                    placeholder="Ej: 8va, +30, Libre, Primera"
                >
                @error('nombre')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
                <p class="mt-1 text-xs text-gray-500">
                    Ejemplos: 8va, 7ma, 6ta, +30, +40, Libre, Primera, Segunda, etc.
                </p>
            </div>

            <!-- Advertencia si está en uso -->
            @if($categoria->torneos()->whereIn('estado', ['borrador', 'activo'])->count() > 0)
                <div class="mb-6 p-4 bg-amber-50 border border-amber-200 rounded-lg">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-amber-600 mt-0.5 mr-2 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div class="text-sm text-amber-800">
                            <p class="font-semibold mb-1">Categoría en uso</p>
                            <p>Esta categoría está siendo usada en {{ $categoria->torneos()->whereIn('estado', ['borrador', 'activo'])->count() }} torneo(s) activo(s). Los cambios afectarán estos torneos.</p>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Botones -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-6 border-t border-gray-200">
                <a href="{{ route('categorias.index') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-center text-sm sm:text-base order-2 sm:order-1">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base order-1 sm:order-2">
                    Guardar Cambios
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
