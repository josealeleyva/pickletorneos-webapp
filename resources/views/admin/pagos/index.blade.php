@extends('admin.layouts.app')

@section('title', 'Pagos')
@section('page-title', 'Gestión de Pagos')

@section('content')
<div class="space-y-6">
    <!-- Métricas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Mes Actual</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($totalMesActual, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="far fa-calendar"></i> {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Histórico</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($totalHistorico, 0, ',', '.') }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-chart-line"></i> Todos los tiempos
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Torneos Pagos vs Gratuitos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $torneosPagos }} / {{ $torneosGratuitos }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-chart-pie"></i> Total general
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-trophy text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Créditos Usados (Mes)</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $creditosUsadosMes }}</p>
                    <p class="text-xs text-gray-500 mt-1">
                        <i class="fas fa-gift"></i> {{ now()->locale('es')->isoFormat('MMMM') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-ticket text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Torneo, organizador..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="pagado" {{ request('estado') == 'pagado' ? 'selected' : '' }}>Pagado</option>
                    <option value="gratuito" {{ request('estado') == 'gratuito' ? 'selected' : '' }}>Gratuito</option>
                    <option value="pendiente" {{ request('estado') == 'pendiente' ? 'selected' : '' }}>Pendiente</option>
                    <option value="cancelado" {{ request('estado') == 'cancelado' ? 'selected' : '' }}>Cancelado</option>
                    <option value="vencido" {{ request('estado') == 'vencido' ? 'selected' : '' }}>Vencido</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Mes</label>
                <input type="month" name="mes" value="{{ request('mes') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Pagos -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Torneo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organizador</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Monto</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Método Pago</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fecha</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pagos as $pago)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    @if($pago->torneo?->banner)
                                        <img src="{{ asset('storage/' . $pago->torneo->banner) }}" alt="{{ $pago->torneo->nombre }}" class="w-10 h-10 rounded object-cover">
                                    @else
                                        <div class="w-10 h-10 rounded bg-gray-200 flex items-center justify-center">
                                            <i class="fas fa-trophy text-gray-400 text-sm"></i>
                                        </div>
                                    @endif
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $pago->torneo?->nombre ?? '(torneo eliminado)' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $pago->torneo?->organizador?->nombre }} {{ $pago->torneo?->organizador?->apellido }}
                                </div>
                                <div class="text-xs text-gray-500">{{ $pago->torneo?->organizador?->email ?? '-' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($pago->estado === 'gratuito')
                                    <span class="text-sm font-bold text-blue-600">GRATIS</span>
                                    @if($pago->credito_referido_id)
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-gift"></i> Crédito usado
                                        </div>
                                    @endif
                                @else
                                    <span class="text-sm font-bold text-gray-800">${{ number_format($pago->monto, 0, ',', '.') }}</span>
                                    @if($pago->descuento_aplicado && $pago->descuento_aplicado > 0)
                                        <div class="text-xs text-green-600 mt-1">
                                            <i class="fas fa-tag"></i> -{{ number_format($pago->descuento_aplicado, 0, ',', '.') }}
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @php
                                    $estadoBadges = [
                                        'pagado' => ['green', 'Pagado'],
                                        'gratuito' => ['blue', 'Gratuito'],
                                        'pendiente' => ['yellow', 'Pendiente'],
                                        'cancelado' => ['red', 'Cancelado'],
                                        'vencido' => ['gray', 'Vencido']
                                    ];
                                    $badge = $estadoBadges[$pago->estado] ?? ['gray', ucfirst($pago->estado)];
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $badge[0] }}-100 text-{{ $badge[0] }}-800">
                                    {{ $badge[1] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                @if($pago->credito_referido_id)
                                    <span class="text-purple-600">
                                        <i class="fas fa-gift"></i> Crédito
                                    </span>
                                @elseif($pago->metodo_pago)
                                    <i class="fab fa-cc-{{ strtolower($pago->metodo_pago) }}"></i>
                                    {{ ucfirst($pago->metodo_pago) }}
                                @elseif($pago->estado === 'gratuito')
                                    <span class="text-blue-600">
                                        <i class="fas fa-star"></i> Primer torneo
                                    </span>
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                {{ $pago->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                @if($pago->torneo)
                                    <a href="{{ route('admin.torneos.show', $pago->torneo) }}" class="text-blue-600 hover:text-blue-900" title="Ver torneo">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <span class="text-gray-300" title="Torneo eliminado">
                                        <i class="fas fa-eye"></i>
                                    </span>
                                @endif
                                @if($pago->external_reference)
                                    <button onclick="showPaymentDetails('{{ $pago->id }}')" class="text-green-600 hover:text-green-900" title="Ver detalles de pago">
                                        <i class="fas fa-info-circle"></i>
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                No se encontraron pagos con los filtros aplicados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $pagos->appends(request()->query())->links() }}
        </div>
    </div>

    <!-- Resumen por Método de Pago -->
    @if($resumenMetodosPago->isNotEmpty())
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-pie text-blue-600 mr-2"></i>
                Resumen por Método de Pago
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @foreach($resumenMetodosPago as $metodo)
                    <div class="border border-gray-200 rounded-lg p-4">
                        <p class="text-sm text-gray-600">{{ ucfirst($metodo->metodo ?? 'Crédito/Gratis') }}</p>
                        <p class="text-2xl font-bold text-gray-800 mt-1">{{ $metodo->cantidad }}</p>
                        <p class="text-sm text-green-600 mt-1">${{ number_format($metodo->total, 0, ',', '.') }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    function showPaymentDetails(pagoId) {
        // Aquí se podría implementar un modal con más detalles del pago
        // Por ahora solo mostramos un alert simple
        alert('Funcionalidad de detalles de pago - ID: ' + pagoId);
    }
</script>
@endpush
