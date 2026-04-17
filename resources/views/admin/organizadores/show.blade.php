@extends('admin.layouts.app')

@section('title', 'Detalle de Organizador')
@section('page-title', 'Detalle de Organizador')

@section('content')
<div class="space-y-6">
    <!-- Botón Volver -->
    <div>
        <a href="{{ route('admin.organizadores.index') }}" class="inline-flex items-center text-brand-600 hover:text-blue-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver a Organizadores
        </a>
    </div>

    <!-- Información del Organizador -->
    <div class="bg-white rounded-lg shadow p-4 md:p-6">
        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6 mb-6">
            <!-- Información del usuario -->
            <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 flex-1">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-blue-500 flex items-center justify-center text-white text-2xl sm:text-3xl font-bold flex-shrink-0">
                    {{ strtoupper(substr($user->nombre, 0, 1))}}{{ strtoupper(substr($user->apellido, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 break-words">{{ $user->nombre }} {{ $user->apellido }}</h2>
                    <p class="text-sm sm:text-base text-gray-600 mt-1 break-all">
                        <i class="fas fa-envelope mr-2"></i>{{ $user->email }}
                    </p>
                    <p class="text-sm sm:text-base text-gray-600 mt-1">
                        <i class="fas fa-calendar mr-2"></i>Registrado el {{ $user->created_at->format('d/m/Y') }}
                    </p>
                    <div class="mt-2">
                        @if($user->cuenta_activa)
                            <span class="inline-flex items-center px-3 py-1 text-xs sm:text-sm font-semibold rounded-full bg-green-100 text-green-800">
                                <i class="fas fa-check-circle mr-1"></i>Activo
                            </span>
                        @else
                            <span class="inline-flex items-center px-3 py-1 text-xs sm:text-sm font-semibold rounded-full bg-red-100 text-red-800">
                                <i class="fas fa-ban mr-1"></i>Inactivo
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Botones de acción -->
            <div class="flex flex-col sm:flex-row lg:flex-col gap-2 w-full sm:w-auto lg:w-48 flex-shrink-0">
                <button onclick="openOtorgarCreditoModal()" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center justify-center">
                    <i class="fas fa-gift mr-2"></i>
                    <span>Otorgar Crédito</span>
                </button>
                <form action="{{ route('admin.organizadores.toggle-estado', $user) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full {{ $user->cuenta_activa ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg font-medium transition flex items-center justify-center">
                        <i class="fas fa-{{ $user->cuenta_activa ? 'ban' : 'check-circle' }} mr-2"></i>
                        <span>{{ $user->cuenta_activa ? 'Desactivar' : 'Activar' }}</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Código de Referido -->
        @if($user->codigo_referido)
            <div class="bg-blue-50 border border-brand-200 rounded-lg p-4">
                <p class="text-sm text-gray-700 mb-2">
                    <i class="fas fa-link mr-2"></i><strong>Código de Referido:</strong>
                </p>
                <code class="text-lg font-mono bg-white px-3 py-2 rounded border border-blue-300">{{ $user->codigo_referido }}</code>
            </div>
        @endif
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Torneos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $totalTorneos }}</p>
                </div>
                <div class="w-12 h-12 bg-accent-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-trophy text-accent-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Total Pagado</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($totalPagado, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Referidos Activos</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">{{ $referidosActivos }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-brand-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm font-medium">Créditos Disponibles</p>
                    <p class="text-3xl font-bold text-gray-800 mt-2">${{ number_format($creditosDisponibles, 0, ',', '.') }}</p>
                </div>
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-gift text-brand-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Torneos del Organizador -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-800">
                <i class="fas fa-trophy text-accent-600 mr-2"></i>
                Torneos Creados
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nombre</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Deporte</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Monto Pagado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fecha Inicio</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($user->torneos as $torneo)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $torneo->nombre }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
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
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">
                                @if($torneo->pago)
                                    @if($torneo->pago->estado === 'pagado')
                                        ${{ number_format($torneo->pago->monto, 0, ',', '.') }}
                                    @elseif($torneo->pago->estado === 'gratuito')
                                        <span class="text-brand-600">GRATIS</span>
                                    @else
                                        <span class="text-gray-400">Pendiente</span>
                                    @endif
                                @else
                                    <span class="text-gray-400">N/A</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                {{ $torneo->fecha_inicio->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                <a href="{{ route('admin.torneos.show', $torneo) }}" class="text-brand-600 hover:text-blue-900" title="Ver torneo">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                Este organizador aún no ha creado torneos
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Referidos Activos -->
    @if($user->referidos->isNotEmpty())
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-800">
                    <i class="fas fa-users text-brand-600 mr-2"></i>
                    Referidos Activos
                </h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Referido</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fecha Registro</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Fecha Activación</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($user->referidos as $referido)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-bold">
                                            {{ strtoupper(substr($referido->referido->nombre, 0, 1))}}{{ strtoupper(substr($referido->referido->apellido, 0, 1)) }}
                                        </div>
                                        <div class="ml-3">
                                            <div class="text-sm font-medium text-gray-900">
                                                {{ $referido->referido->nombre }} {{ $referido->referido->apellido }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    {{ $referido->referido->email }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                    {{ $referido->fecha_registro->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-600">
                                    {{ $referido->fecha_activacion?->format('d/m/Y') ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    @php
                                        $estadoReferidoBadges = [
                                            'pendiente' => ['yellow', 'Pendiente'],
                                            'activo' => ['green', 'Activo'],
                                            'expirado' => ['red', 'Expirado']
                                        ];
                                        $estadoBadge = $estadoReferidoBadges[$referido->estado] ?? ['gray', ucfirst($referido->estado)];
                                    @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-{{ $estadoBadge[0] }}-100 text-{{ $estadoBadge[0] }}-800">
                                        {{ $estadoBadge[1] }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>

<!-- Modal Otorgar Crédito -->
<div id="otorgarCreditoModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-xl font-bold text-gray-800">
                <i class="fas fa-gift text-green-600 mr-2"></i>
                Otorgar Crédito
            </h3>
            <button onclick="closeOtorgarCreditoModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <form action="{{ route('admin.organizadores.otorgar-credito', $user) }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Monto del Crédito
                    <span class="text-gray-500 text-xs">(Opcional - si se deja vacío, se usará el precio estándar del torneo)</span>
                </label>
                <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input type="number" name="monto" step="0.01" min="0" placeholder="25000" class="w-full pl-8 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Motivo <span class="text-red-500">*</span>
                </label>
                <textarea name="motivo" rows="3" required placeholder="Ej: Compensación por error en el sistema, premio por promoción, etc." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"></textarea>
            </div>

            <div class="bg-blue-50 border border-brand-200 rounded-lg p-3">
                <p class="text-sm text-blue-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    Este crédito estará disponible para que el organizador lo use en su próximo torneo.
                </p>
            </div>

            <div class="flex space-x-3">
                <button type="button" onclick="closeOtorgarCreditoModal()" class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium transition">
                    Cancelar
                </button>
                <button type="submit" class="flex-1 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-gift mr-2"></i>
                    Otorgar Crédito
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function openOtorgarCreditoModal() {
        document.getElementById('otorgarCreditoModal').classList.remove('hidden');
    }

    function closeOtorgarCreditoModal() {
        document.getElementById('otorgarCreditoModal').classList.add('hidden');
    }

    // Cerrar modal al hacer clic fuera de él
    document.getElementById('otorgarCreditoModal')?.addEventListener('click', function(e) {
        if (e.target === this) {
            closeOtorgarCreditoModal();
        }
    });

    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeOtorgarCreditoModal();
        }
    });
</script>
@endpush
