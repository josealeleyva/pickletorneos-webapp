@extends('layouts.dashboard')

@section('title', 'Categorías')
@section('page-title', 'Mis Categorías')

@section('content')
<div class="space-y-6">
    <!-- Header con botón de crear -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-gray-600 text-sm sm:text-base">Gestiona las categorías para tus torneos</p>
        </div>
        <a href="{{ route('categorias.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 w-full sm:w-auto justify-center text-sm sm:text-base">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Categoría
        </a>
    </div>

    @if($categoriasPorDeporte->count() > 0)
        <!-- Categorías agrupadas por deporte -->
        @foreach($categoriasPorDeporte as $deporteNombre => $categorias)
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                <!-- Header del deporte -->
                <div class="bg-gradient-to-r from-brand-500 to-purple-600 px-4 sm:px-6 py-3 sm:py-4">
                    <h3 class="text-lg sm:text-xl font-bold text-white">{{ $deporteNombre }}</h3>
                </div>

                <!-- Lista de categorías -->
                <div class="divide-y divide-gray-200">
                    @foreach($categorias as $categoria)
                        <div class="px-4 sm:px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                                <div class="flex-1">
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-800">{{ $categoria->nombre }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-500 mt-1">
                                        @if($categoria->torneos()->count() > 0)
                                            Usada en {{ $categoria->torneos()->count() }} torneo(s)
                                        @else
                                            Sin torneos asociados
                                        @endif
                                    </p>
                                </div>

                                <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                    <a href="{{ route('categorias.edit', $categoria) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 py-2 text-brand-600 hover:text-brand-700 hover:bg-brand-50 rounded-lg font-medium text-xs sm:text-sm transition">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Editar
                                    </a>

                                    <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" class="inline flex-1 sm:flex-none" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg font-medium text-xs sm:text-sm transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No tienes categorías</h3>
                <p class="text-gray-600 mb-6">
                    Comienza creando categorías para organizar tus torneos por edad, nivel o división.
                </p>
                <a href="{{ route('categorias.create') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Primera Categoría
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
