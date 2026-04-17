@extends('admin.layouts.app')

@section('title', 'Dashboard Administrativo')
@section('page-title', 'Dashboard')

@section('content')
<div class="space-y-6">
    <!-- Métricas principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Organizadores -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Organizadores</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalOrganizadores }}</p>
                    <p class="text-sm text-green-600 mt-1">
                        <i class="fas fa-check-circle"></i> {{ $organizadoresActivos }} activos
                    </p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Torneos -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Torneos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTorneos }}</p>
                    <p class="text-sm text-blue-600 mt-1">
                        <i class="fas fa-fire"></i> {{ $torneosActivos }} activos
                    </p>
                </div>
                <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-trophy text-accent-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Ingresos del Mes -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Ingresos del Mes</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($ingresosMesActual, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="far fa-calendar"></i> {{ now()->locale('es')->isoFormat('MMMM YYYY') }}
                    </p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Ingresos Totales -->
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Ingresos Totales</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($ingresosTotales, 0, ',', '.') }}</p>
                    <p class="text-sm text-gray-600 mt-1">
                        <i class="fas fa-chart-line"></i> Histórico
                    </p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-wallet text-purple-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Gráfico de Ingresos -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-chart-bar text-blue-600 mr-2"></i>
                Ingresos por Mes (Últimos 6 meses)
            </h3>
            <canvas id="ingresosChart" height="100"></canvas>
        </div>

        <!-- Torneos por Estado -->
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">
                <i class="fas fa-list-check text-purple-600 mr-2"></i>
                Torneos por Estado
            </h3>
            <div class="space-y-3">
                @foreach(['borrador' => ['Borrador', 'gray'], 'activo' => ['Activo', 'blue'], 'en_curso' => ['En Curso', 'green'], 'finalizado' => ['Finalizado', 'purple'], 'cancelado' => ['Cancelado', 'red']] as $estado => $config)
                    @php $count = $torneosPorEstado[$estado] ?? 0; @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <span class="w-3 h-3 rounded-full bg-{{ $config[1] }}-500 mr-2"></span>
                            <span class="text-gray-700">{{ $config[0] }}</span>
                        </div>
                        <span class="font-semibold text-gray-800">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Actividad Reciente -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-800 mb-4">
            <i class="fas fa-clock-rotate-left text-brand-600 mr-2"></i>
            Actividad Reciente
        </h3>
        <div class="space-y-4">
            @forelse($actividadReciente as $actividad)
                <div class="flex items-start">
                    <div class="w-10 h-10 rounded-full bg-{{ $actividad['color'] }}-100 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-{{ $actividad['icono'] }} text-{{ $actividad['color'] }}-600"></i>
                    </div>
                    <div class="ml-4 flex-1">
                        <p class="text-gray-800">{{ $actividad['descripcion'] }}</p>
                        <p class="text-sm text-gray-500 mt-1">
                            <i class="far fa-clock mr-1"></i>
                            {{ $actividad['fecha']->diffForHumans() }}
                        </p>
                    </div>
                </div>
            @empty
                <p class="text-gray-500 text-center py-4">No hay actividad reciente</p>
            @endforelse
        </div>
    </div>

    @if($sugerenciasPendientes > 0)
        <!-- Alert de Sugerencias Pendientes -->
        <div class="bg-accent-50 border-l-4 border-accent-400 p-4 rounded">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-accent-600 text-xl mr-3"></i>
                <div class="flex-1">
                    <p class="text-accent-800 font-medium">
                        Tienes {{ $sugerenciasPendientes }} {{ $sugerenciasPendientes == 1 ? 'sugerencia pendiente' : 'sugerencias pendientes' }}
                    </p>
                    <p class="text-accent-700 text-sm mt-1">Revisa las sugerencias de los organizadores</p>
                </div>
                <a href="{{ route('admin.sugerencias.index') }}" class="ml-4 bg-accent-500 hover:bg-accent-600 text-white px-4 py-2 rounded font-medium transition">
                    Ver Sugerencias
                </a>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Gráfico de ingresos
    const ctx = document.getElementById('ingresosChart').getContext('2d');
    const data = @json($ingresosPorMes);

    const labels = data.map(item => {
        const [year, month] = item.mes.split('-');
        const date = new Date(year, month - 1);
        return date.toLocaleDateString('es-AR', { month: 'short', year: 'numeric' });
    });

    const values = data.map(item => parseFloat(item.total));

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Ingresos ($)',
                data: values,
                backgroundColor: 'rgba(59, 130, 246, 0.5)',
                borderColor: 'rgb(59, 130, 246)',
                borderWidth: 2,
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toLocaleString('es-AR');
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value.toLocaleString('es-AR');
                        }
                    }
                }
            }
        }
    });
</script>
@endpush
