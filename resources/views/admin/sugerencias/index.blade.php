@extends('admin.layouts.app')

@section('title', 'Sugerencias')
@section('page-title', 'Gestión de Sugerencias')

@section('content')
<div class="space-y-6">
    <!-- Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Nuevas</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalNuevas }}</p>
                </div>
                <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-bell text-accent-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">En Revisión</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalEnRevision }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-eye text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Pendientes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalPendientes }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Respondidas</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalRespondidas }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="nueva" {{ request('estado') == 'nueva' ? 'selected' : '' }}>Nueva</option>
                    <option value="en_revision" {{ request('estado') == 'en_revision' ? 'selected' : '' }}>En Revisión</option>
                    <option value="respondida" {{ request('estado') == 'respondida' ? 'selected' : '' }}>Respondida</option>
                    <option value="cerrada" {{ request('estado') == 'cerrada' ? 'selected' : '' }}>Cerrada</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                <select name="tipo" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="sugerencia" {{ request('tipo') == 'sugerencia' ? 'selected' : '' }}>Sugerencia</option>
                    <option value="soporte" {{ request('tipo') == 'soporte' ? 'selected' : '' }}>Soporte</option>
                    <option value="bug" {{ request('tipo') == 'bug' ? 'selected' : '' }}>Bug</option>
                    <option value="otro" {{ request('tipo') == 'otro' ? 'selected' : '' }}>Otro</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-search mr-2"></i>Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Sugerencias -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Usuario</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Tipo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Asunto</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sugerencias as $sugerencia)
                        <tr class="hover:bg-gray-50 {{ $sugerencia->estado === 'nueva' ? 'bg-accent-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr($sugerencia->user->nombre, 0, 1))}}{{ strtoupper(substr($sugerencia->user->apellido, 0, 1)) }}
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $sugerencia->user->nombre }} {{ $sugerencia->user->apellido }}
                                        </div>
                                        <div class="text-xs text-gray-500">{{ $sugerencia->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $tipoBadges = [
                                        'sugerencia' => ['blue', 'fa-lightbulb', 'Sugerencia'],
                                        'soporte' => ['yellow', 'fa-headset', 'Soporte'],
                                        'bug' => ['red', 'fa-bug', 'Bug'],
                                        'otro' => ['gray', 'fa-question', 'Otro']
                                    ];
                                    $tipo = $tipoBadges[$sugerencia->tipo] ?? ['gray', 'fa-circle', ucfirst($sugerencia->tipo)];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $tipo[0] }}-100 text-{{ $tipo[0] }}-800">
                                    <i class="fas {{ $tipo[1] }} mr-1"></i>{{ $tipo[2] }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">{{ $sugerencia->asunto }}</div>
                                <div class="text-xs text-gray-500 mt-1 truncate max-w-md">
                                    {{ Str::limit($sugerencia->mensaje, 80) }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $estadoBadges = [
                                        'nueva' => ['yellow', 'Nueva'],
                                        'en_revision' => ['blue', 'En Revisión'],
                                        'respondida' => ['green', 'Respondida'],
                                        'cerrada' => ['gray', 'Cerrada']
                                    ];
                                    $estado = $estadoBadges[$sugerencia->estado] ?? ['gray', ucfirst($sugerencia->estado)];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $estado[0] }}-100 text-{{ $estado[0] }}-800">
                                    {{ $estado[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                <div>{{ $sugerencia->created_at->format('d/m/Y') }}</div>
                                <div class="text-xs text-gray-500">{{ $sugerencia->created_at->format('H:i') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('admin.sugerencias.show', $sugerencia) }}" class="text-blue-600 hover:text-blue-900" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                No se encontraron sugerencias con los filtros aplicados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $sugerencias->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Distribución por Tipo -->
    @if($distribucionTipo->isNotEmpty())
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-purple-600 mr-2"></i>
                Distribución por Tipo
            </h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @foreach($distribucionTipo as $tipo)
                    @php
                        $tipoConfig = [
                            'sugerencia' => ['blue', 'fa-lightbulb'],
                            'soporte' => ['yellow', 'fa-headset'],
                            'bug' => ['red', 'fa-bug'],
                            'otro' => ['gray', 'fa-question']
                        ];
                        $config = $tipoConfig[$tipo->tipo] ?? ['gray', 'fa-circle'];
                    @endphp
                    <div class="border border-{{ $config[0] }}-200 bg-{{ $config[0] }}-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <i class="fas {{ $config[1] }} text-{{ $config[0] }}-600 text-2xl"></i>
                            <p class="text-2xl font-bold text-{{ $config[0] }}-800">{{ $tipo->cantidad }}</p>
                        </div>
                        <p class="text-sm text-{{ $config[0] }}-600 mt-2">{{ ucfirst($tipo->tipo) }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
