@extends('layouts.dashboard')

@section('title', 'Canchas - ' . $complejo->nombre)
@section('page-title', 'Canchas de ' . $complejo->nombre)

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Breadcrumb -->
    <nav class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600">
            <li>
                <a href="{{ route('complejos.index') }}" class="hover:text-brand-600">Complejos</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">{{ $complejo->nombre }}</li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Canchas</li>
        </ol>
    </nav>

    <!-- Header con botón de crear -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <p class="text-gray-600 text-sm sm:text-base">Gestiona las canchas de {{ $complejo->nombre }}</p>
        </div>
        <a href="{{ route('complejos.canchas.create', $complejo) }}" class="inline-flex items-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 w-full sm:w-auto justify-center text-sm sm:text-base">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
            </svg>
            Nueva Cancha
        </a>
    </div>

    @if($canchas->count() > 0)
        <!-- Tabla de canchas - Desktop -->
        <div class="hidden sm:block bg-white rounded-lg shadow-sm overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Número
                        </th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Nombre
                        </th>
                        <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($canchas as $cancha)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-brand-100 flex items-center justify-center">
                                        <span class="text-brand-600 font-bold">{{ $cancha->numero }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $cancha->nombre }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-3">
                                    <a href="{{ route('complejos.canchas.edit', [$complejo, $cancha]) }}" class="text-brand-600 hover:text-brand-900">
                                        Editar
                                    </a>
                                    <form action="{{ route('complejos.canchas.destroy', [$complejo, $cancha]) }}" method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta cancha?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Eliminar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Cards de canchas - Mobile -->
        <div class="sm:hidden space-y-3">
            @foreach($canchas as $cancha)
                <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                    <div class="p-4">
                        <div class="flex items-center justify-between mb-3">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-12 w-12 rounded-full bg-brand-100 flex items-center justify-center mr-3">
                                    <span class="text-brand-600 font-bold text-lg">{{ $cancha->numero }}</span>
                                </div>
                                <div>
                                    <h3 class="text-base font-semibold text-gray-900">{{ $cancha->nombre }}</h3>
                                    <p class="text-xs text-gray-500">Cancha #{{ $cancha->numero }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-2 pt-3 border-t border-gray-200">
                            <a href="{{ route('complejos.canchas.edit', [$complejo, $cancha]) }}" class="flex-1 text-center px-3 py-2 text-brand-600 border border-brand-600 rounded-lg hover:bg-brand-50 text-sm font-medium">
                                Editar
                            </a>
                            <form action="{{ route('complejos.canchas.destroy', [$complejo, $cancha]) }}" method="POST" class="flex-1" onsubmit="return confirm('¿Estás seguro de eliminar esta cancha?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full px-3 py-2 text-red-600 border border-red-600 rounded-lg hover:bg-red-50 text-sm font-medium">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Info footer -->
        <div class="bg-white rounded-lg shadow-sm px-4 sm:px-6 py-3 sm:py-4">
            <p class="text-sm text-gray-600">
                Total de canchas: <span class="font-semibold text-gray-900">{{ $canchas->count() }}</span>
            </p>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-800 mb-2">No hay canchas en este complejo</h3>
                <p class="text-gray-600 mb-6">
                    Comienza agregando la primera cancha a <strong>{{ $complejo->nombre }}</strong>.
                </p>
                <a href="{{ route('complejos.canchas.create', $complejo) }}" class="inline-flex items-center px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Crear Primera Cancha
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
