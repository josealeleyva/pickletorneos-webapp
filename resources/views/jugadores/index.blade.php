@extends('layouts.dashboard')

@section('title', 'Jugadores')
@section('page-title', 'Mis Jugadores')

@section('content')
<div class="space-y-6">
    <!-- Header con buscador y botón de crear -->
    <div class="flex flex-col gap-4">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <p class="text-gray-600 text-sm sm:text-base">Gestiona los jugadores de tus torneos</p>
            </div>
            <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                <!-- Exportar -->
                <a href="{{ route('jugadores.exportar') }}" class="inline-flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                    </svg>
                    Exportar
                </a>
                <!-- Importar -->
                <button type="button" onclick="document.getElementById('modal-importar').classList.remove('hidden')" class="inline-flex items-center justify-center px-4 py-2 bg-accent-500 hover:bg-accent-600 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l4-4m0 0l4 4m-4-4v12"></path>
                    </svg>
                    Importar
                </button>
                <!-- Nuevo Jugador -->
                <a href="{{ route('jugadores.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Nuevo Jugador
                </a>
            </div>
        </div>

        <!-- Buscador -->
        <div class="bg-white rounded-lg shadow-sm p-4">
            <form method="GET" action="{{ route('jugadores.index') }}" class="flex gap-2">
                <div class="flex-1 relative">
                    <input
                        type="text"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Buscar por nombre, apellido, DNI, email o teléfono..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-brand-500 focus:border-transparent"
                    >
                    <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg transition duration-200">
                    Buscar
                </button>
                @if(request('search'))
                    <a href="{{ route('jugadores.index') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-semibold rounded-lg transition duration-200">
                        Limpiar
                    </a>
                @endif
            </form>
        </div>
    </div>

    @if(session('importacion_resumen'))
        @php $resumen = session('importacion_resumen'); @endphp
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-4 sm:px-6 py-3">
                <h3 class="text-base sm:text-lg font-bold text-white">Resultado de la Importación</h3>
            </div>
            <div class="p-4 sm:p-6 space-y-4">
                <!-- Contadores resumen -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-green-700">{{ $resumen['importados'] }}</p>
                        <p class="text-sm text-green-600">jugador(es) importado(s)</p>
                    </div>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-yellow-700">{{ count($resumen['duplicados_dni']) }}</p>
                        <p class="text-sm text-yellow-600">omitido(s) por DNI duplicado</p>
                    </div>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-3 text-center">
                        <p class="text-2xl font-bold text-red-700">{{ count($resumen['errores']) }}</p>
                        <p class="text-sm text-red-600">fila(s) con errores</p>
                    </div>
                </div>

                <!-- Detalle de DNIs duplicados -->
                @if(!empty($resumen['duplicados_dni']))
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-yellow-800 mb-2">DNIs omitidos (ya existen en tu lista):</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($resumen['duplicados_dni'] as $dni)
                                <span class="px-2 py-0.5 bg-yellow-200 text-yellow-900 rounded text-xs font-mono">{{ $dni }}</span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Detalle de errores de validación -->
                @if(!empty($resumen['errores']))
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm font-semibold text-red-800 mb-3">Filas con errores (no fueron importadas):</p>
                        <div class="space-y-2">
                            @foreach($resumen['errores'] as $error)
                                <div class="text-sm text-red-700">
                                    <span class="font-semibold">Fila {{ $error['fila'] }}:</span>
                                    @foreach($error['errores'] as $msg)
                                        <span>{{ $msg }}</span>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if($jugadores->count() > 0)
        <!-- Tabla de jugadores -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <!-- Header -->
            <div class="bg-gradient-to-r from-brand-700 to-brand-500 px-4 sm:px-6 py-3 sm:py-4">
                <h3 class="text-lg sm:text-xl font-bold text-white">Lista de Jugadores</h3>
            </div>

            <!-- Lista de jugadores -->
            <div class="divide-y divide-gray-200">
                @foreach($jugadores as $jugador)
                    <div class="px-4 sm:px-6 py-4 hover:bg-gray-50 transition-colors duration-200">
                        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
                            <div class="flex-1 flex items-center gap-4">
                                <!-- Foto -->
                                <div class="flex-shrink-0">
                                    @if($jugador->foto)
                                        <img src="{{ asset('storage/' . $jugador->foto) }}" alt="{{ $jugador->nombre_completo }}" class="w-12 h-12 rounded-full object-cover border-2 border-brand-200">
                                    @else
                                        <div class="w-12 h-12 rounded-full bg-brand-100 flex items-center justify-center">
                                            <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Informaci�n -->
                                <div class="flex-1 min-w-0">
                                    <h4 class="text-base sm:text-lg font-semibold text-gray-800 truncate">{{ $jugador->nombre_completo }}</h4>
                                    <div class="flex flex-wrap gap-x-4 gap-y-1 text-xs sm:text-sm text-gray-500 mt-1">
                                        @if($jugador->ranking)
                                            <span class="inline-flex items-center gap-1 px-2 py-0.5 bg-brand-100 text-brand-700 rounded-full font-medium text-xs">
                                                Ranking: {{ $jugador->ranking }}
                                            </span>
                                        @endif
                                        @if($jugador->dni)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                                                </svg>
                                                DNI: {{ $jugador->dni }}
                                            </span>
                                        @endif
                                        @if($jugador->email)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                                {{ $jugador->email }}
                                            </span>
                                        @endif
                                        @if($jugador->telefono)
                                            <span class="flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                </svg>
                                                {{ $jugador->telefono }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Acciones -->
                            <div class="flex items-center gap-2 sm:gap-3 w-full sm:w-auto">
                                <a href="{{ route('jugadores.edit', $jugador) }}" class="flex-1 sm:flex-none inline-flex items-center justify-center px-3 py-2 text-brand-600 hover:text-brand-700 hover:bg-brand-50 rounded-lg font-medium text-xs sm:text-sm transition">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Editar
                                </a>

                                @php
                                    $tieneEquipos = $jugador->equipos()->exists();
                                    $puedeEliminar = !$tieneEquipos;
                                @endphp

                                @if($puedeEliminar)
                                    <form action="{{ route('jugadores.destroy', $jugador) }}" method="POST" class="inline flex-1 sm:flex-none" onsubmit="return confirm('¿Estás seguro de eliminar este jugador?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-lg font-medium text-xs sm:text-sm transition">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Eliminar
                                        </button>
                                    </form>
                                @else
                                    <div class="flex-1 sm:flex-none relative group">
                                        <button type="button" disabled class="w-full sm:w-auto inline-flex items-center justify-center px-3 py-2 text-gray-400 bg-gray-100 rounded-lg font-medium text-xs sm:text-sm cursor-not-allowed opacity-60">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                            Eliminar
                                        </button>
                                        <!-- Tooltip -->
                                        <div class="hidden group-hover:block absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-3 py-2 bg-gray-900 text-white text-xs rounded-lg whitespace-nowrap z-10">
                                            @if($tieneEquipos)
                                                Jugador en torneos
                                            @endif
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1">
                                                <div class="border-4 border-transparent border-t-gray-900"></div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white rounded-lg shadow-sm p-12 text-center">
            <div class="max-w-md mx-auto">
                @if(request('search'))
                    <!-- No se encontraron resultados -->
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-gray-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No se encontraron jugadores</h3>
                    <p class="text-gray-600 mb-6">
                        No hay jugadores que coincidan con "<strong>{{ request('search') }}</strong>".
                    </p>
                    <a href="{{ route('jugadores.index') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Limpiar Búsqueda
                    </a>
                @else
                    <!-- No hay jugadores -->
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-brand-100 rounded-full mb-4">
                        <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800 mb-2">No tienes jugadores</h3>
                    <p class="text-gray-600 mb-6">
                        Comienza agregando jugadores para inscribirlos en tus torneos.
                    </p>
                    <a href="{{ route('jugadores.create') }}" class="inline-flex items-center px-6 py-3 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-md transition duration-200">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Agregar Primer Jugador
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<!-- Modal de Importación -->
<div id="modal-importar" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black bg-opacity-50" onclick="document.getElementById('modal-importar').classList.add('hidden')"></div>

    <!-- Contenido -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg relative z-10">
            <!-- Header -->
            <div class="flex items-center justify-between p-4 sm:p-6 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">Importar Jugadores desde Excel</h2>
                <button type="button" onclick="document.getElementById('modal-importar').classList.add('hidden')" class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <!-- Body -->
            <div class="p-4 sm:p-6 space-y-4">
                <!-- Instrucciones de formato -->
                <div class="bg-blue-50 border border-brand-200 rounded-lg p-4 text-sm">
                    <p class="font-semibold text-blue-800 mb-1">Formato requerido del archivo:</p>
                    <p class="text-blue-700 mb-3 text-xs">La fila 1 debe tener los encabezados, las siguientes filas los datos. <span class="text-red-600 font-semibold">* Obligatorio</span></p>
                    <div class="overflow-x-auto">
                        <table class="text-xs border-collapse">
                            <thead>
                                <tr>
                                    <th class="border border-blue-400 px-3 py-1.5 bg-brand-600 text-white font-semibold font-mono whitespace-nowrap">Nombre <span class="text-red-300">*</span></th>
                                    <th class="border border-blue-400 px-3 py-1.5 bg-brand-600 text-white font-semibold font-mono whitespace-nowrap">Apellido <span class="text-red-300">*</span></th>
                                    <th class="border border-blue-400 px-3 py-1.5 bg-brand-600 text-white font-semibold font-mono whitespace-nowrap">Ranking</th>
                                    <th class="border border-blue-400 px-3 py-1.5 bg-brand-600 text-white font-semibold font-mono whitespace-nowrap">Teléfono</th>
                                    <th class="border border-blue-400 px-3 py-1.5 bg-brand-600 text-white font-semibold font-mono whitespace-nowrap">Email</th>
                                    <th class="border border-blue-400 px-3 py-1.5 bg-brand-600 text-white font-semibold font-mono whitespace-nowrap">DNI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class="bg-gray-100 text-gray-500 italic">
                                    <td class="border border-brand-200 px-3 py-1.5">Juan</td>
                                    <td class="border border-brand-200 px-3 py-1.5">Pérez</td>
                                    <td class="border border-brand-200 px-3 py-1.5">1500</td>
                                    <td class="border border-brand-200 px-3 py-1.5">3415001234</td>
                                    <td class="border border-brand-200 px-3 py-1.5">juan@ej.com</td>
                                    <td class="border border-brand-200 px-3 py-1.5">30123456</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Descarga de plantilla -->
                <a href="{{ route('jugadores.plantilla') }}" class="flex items-center gap-3 p-3 bg-gray-50 border border-gray-200 rounded-lg hover:bg-gray-100 transition">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm font-semibold text-gray-800">Descargar plantilla de ejemplo</p>
                        <p class="text-xs text-gray-500">Archivo Excel con encabezados y una fila de ejemplo</p>
                    </div>
                </a>

                <!-- Formulario de subida -->
                <form action="{{ route('jugadores.importar') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Seleccionar archivo Excel</label>
                        <input type="file" name="archivo" accept=".xlsx,.xls,.csv" required
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100 border border-gray-300 rounded-lg cursor-pointer p-1">
                        <p class="mt-1 text-xs text-gray-500">Formatos aceptados: .xlsx, .xls, .csv — Máximo 5MB</p>
                        @error('archivo')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="bg-accent-50 border border-accent-200 rounded-lg p-3 text-xs text-accent-800">
                        <strong>Importante:</strong> Los jugadores con DNI duplicado serán omitidos automáticamente. Las filas con datos inválidos no serán importadas y se mostrará un reporte con los errores.
                    </div>
                    <div class="flex flex-col sm:flex-row gap-3 pt-2">
                        <button type="button" onclick="document.getElementById('modal-importar').classList.add('hidden')" class="flex-1 px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition text-sm font-medium">
                            Cancelar
                        </button>
                        <button type="submit" class="flex-1 px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg transition text-sm">
                            Importar Jugadores
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->has('archivo'))
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('modal-importar').classList.remove('hidden');
    });
</script>
@endif

@endsection
