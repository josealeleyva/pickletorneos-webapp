@extends('admin.layouts.app')

@section('title', 'Torneos')
@section('page-title', 'Gestión de Torneos')

@section('content')
<div class="space-y-6">
    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre del torneo..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="borrador" {{ request('estado') == 'borrador' ? 'selected' : '' }}>Borrador</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                    <option value="en_curso" {{ request('estado') == 'en_curso' ? 'selected' : '' }}>En Curso</option>
                    <option value="finalizado" {{ request('estado') == 'finalizado' ? 'selected' : '' }}>Finalizado</option>
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Deporte</label>
                <select name="deporte" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    @foreach($deportes as $deporte)
                        <option value="{{ $deporte->id }}" {{ request('deporte') == $deporte->id ? 'selected' : '' }}>
                            {{ $deporte->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Resumen de Estadísticas -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs font-medium">TOTAL</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $estadisticas['total'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-gray-500 text-xs font-medium">BORRADOR</p>
            <p class="text-2xl font-bold text-gray-600 mt-1">{{ $estadisticas['borrador'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-blue-600 text-xs font-medium">ACTIVOS</p>
            <p class="text-2xl font-bold text-blue-600 mt-1">{{ $estadisticas['activo'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-green-600 text-xs font-medium">EN CURSO</p>
            <p class="text-2xl font-bold text-green-600 mt-1">{{ $estadisticas['en_curso'] ?? 0 }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-purple-600 text-xs font-medium">FINALIZADOS</p>
            <p class="text-2xl font-bold text-purple-600 mt-1">{{ $estadisticas['finalizado'] ?? 0 }}</p>
        </div>
    </div>

    <!-- Tabla de Torneos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Torneo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organizador</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Deporte</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Monto Pagado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($torneos as $torneo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($torneo->banner)
                                        <img src="{{ asset('storage/' . $torneo->banner) }}" alt="{{ $torneo->nombre }}" class="w-12 h-12 rounded object-cover">
                                    @else
                                        <div class="w-12 h-12 rounded bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-trophy text-gray-400"></i>
                                        </div>
                                    @endif
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $torneo->nombre }}</div>
                                        <div class="text-xs text-gray-500">{{ $torneo->complejoDeportivo->nombre ?? 'N/A' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold">
                                        {{ strtoupper(substr($torneo->organizador->nombre, 0, 1))}}{{ strtoupper(substr($torneo->organizador->apellido, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $torneo->organizador->nombre }} {{ $torneo->organizador->apellido }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $torneo->organizador->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                <i class="fas fa-{{ $torneo->deporte->icono ?? 'trophy' }} mr-1"></i>
                                {{ $torneo->deporte->nombre ?? 'N/A' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $estadoBadges = [
                                        'borrador' => ['gray', 'Borrador'],
                                        'activo' => ['blue', 'Activo'],
                                        'en_curso' => ['green', 'En Curso'],
                                        'finalizado' => ['purple', 'Finalizado'],
                                        'cancelado' => ['red', 'Cancelado']
                                    ];
                                    $badge = $estadoBadges[$torneo->estado] ?? ['gray', ucfirst($torneo->estado)];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $badge[0] }}-100 text-{{ $badge[0] }}-800">
                                    {{ $badge[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                {{ $torneo->fecha_inicio->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold">
                                @if($torneo->pago)
                                    @if($torneo->pago->estado === 'pagado')
                                        <span class="text-green-600">${{ number_format($torneo->pago->monto, 0, ',', '.') }}</span>
                                    @elseif($torneo->pago->estado === 'gratuito')
                                        <span class="text-blue-600">GRATIS</span>
                                    @elseif($torneo->pago->estado === 'pendiente')
                                        <span class="text-accent-600">Pendiente</span>
                                    @else
                                        <span class="text-gray-400">{{ ucfirst($torneo->pago->estado) }}</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('admin.torneos.show', $torneo) }}" class="text-blue-600 hover:text-blue-900" title="Ver torneo">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No se encontraron torneos con los filtros aplicados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $torneos->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
