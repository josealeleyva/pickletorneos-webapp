@extends('admin.layouts.app')

@section('title', 'Detalles del Torneo')
@section('page-title', 'Detalles del Torneo')

@section('content')
<div class="space-y-6">
    <!-- Header con Botón de Volver -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <a href="{{ route('admin.torneos.index') }}" class="text-gray-600 hover:text-gray-900 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Volver a Torneos</span>
        </a>
        <a href="{{ route('torneos.public', $torneo->id) }}" target="_blank" class="bg-brand-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition flex items-center gap-2">
            <i class="fas fa-external-link-alt"></i>
            <span>Vista Pública</span>
        </a>
    </div>

    <!-- Información Principal -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Banner -->
        @if($torneo->banner)
            <div class="h-48 bg-cover bg-center" style="background-image: url('{{ asset('storage/' . $torneo->banner) }}')"></div>
        @else
            <div class="h-48 bg-gradient-to-r from-brand-700 to-brand-500 flex items-center justify-center">
                <i class="fas fa-trophy text-white text-6xl opacity-50"></i>
            </div>
        @endif

        <div class="p-4 md:p-6">
            <!-- Título y Estado -->
            <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">{{ $torneo->nombre }}</h1>
                    <div class="flex flex-wrap items-center gap-3 text-sm text-gray-600">
                        <span class="flex items-center gap-1">
                            <i class="fas fa-{{ $torneo->deporte->icono ?? 'trophy' }}"></i>
                            {{ $torneo->deporte->nombre }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $torneo->complejo->nombre }}
                        </span>
                        <span class="flex items-center gap-1">
                            <i class="fas fa-calendar"></i>
                            {{ $torneo->fecha_inicio->format('d/m/Y') }} - {{ $torneo->fecha_fin->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
                <div class="flex flex-col gap-2">
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
                    <span class="px-4 py-2 text-sm font-semibold rounded-full bg-{{ $badge[0] }}-100 text-{{ $badge[0] }}-800 text-center">
                        {{ $badge[1] }}
                    </span>
                </div>
            </div>

            <!-- Grid de Información -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Organizador -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-user"></i>
                        Organizador
                    </h3>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                            {{ strtoupper(substr($torneo->organizador->nombre, 0, 1))}}{{ strtoupper(substr($torneo->organizador->apellido, 0, 1)) }}
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $torneo->organizador->nombre }} {{ $torneo->organizador->apellido }}</p>
                            <p class="text-xs text-gray-500">{{ $torneo->organizador->email }}</p>
                        </div>
                    </div>
                    <a href="{{ route('admin.organizadores.show', $torneo->organizador) }}" class="mt-3 block text-sm text-brand-600 hover:text-blue-900">
                        Ver perfil completo <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>

                <!-- Formato -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-sitemap"></i>
                        Formato
                    </h3>
                    <p class="text-lg font-semibold text-gray-900">{{ $torneo->formato->nombre ?? 'N/A' }}</p>
                    @if($torneo->categorias->isNotEmpty())
                        <p class="text-sm text-gray-600 mt-1">{{ $torneo->categorias->first()->nombre }}</p>
                    @endif
                </div>

                <!-- Pago -->
                <div class="border border-gray-200 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-500 mb-2 flex items-center gap-2">
                        <i class="fas fa-credit-card"></i>
                        Estado de Pago
                    </h3>
                    @if($torneo->pago)
                        @if($torneo->pago->estado === 'pagado')
                            <p class="text-lg font-bold text-green-600">${{ number_format($torneo->pago->monto, 0, ',', '.') }}</p>
                            <p class="text-xs text-gray-500 mt-1">Pagado el {{ $torneo->pago->created_at->format('d/m/Y') }}</p>
                        @elseif($torneo->pago->estado === 'gratuito')
                            <p class="text-lg font-bold text-brand-600">GRATUITO</p>
                            @if($torneo->pago->credito_referido_id)
                                <p class="text-xs text-gray-500 mt-1">Crédito de referido usado</p>
                            @else
                                <p class="text-xs text-gray-500 mt-1">Primer torneo gratis</p>
                            @endif
                        @else
                            <p class="text-lg font-bold text-accent-600">{{ ucfirst($torneo->pago->estado) }}</p>
                        @endif
                    @else
                        <p class="text-lg text-gray-400">Sin información de pago</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Torneo -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">EQUIPOS</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_equipos'] }}</p>
                </div>
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-brand-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">TOTAL PARTIDOS</p>
                    <p class="text-2xl font-bold text-gray-800 mt-1">{{ $stats['total_partidos'] }}</p>
                </div>
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-calendar-check text-brand-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">JUGADOS</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ $stats['partidos_jugados'] }}</p>
                </div>
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-4">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-xs font-medium">PENDIENTES</p>
                    <p class="text-2xl font-bold text-accent-600 mt-1">{{ $stats['partidos_pendientes'] }}</p>
                </div>
                <div class="w-10 h-10 bg-accent-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-accent-600"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Equipos Inscritos -->
    @if($torneo->equipos->isNotEmpty())
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-4 md:px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h3 class="text-lg font-semibold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users text-brand-600"></i>
                    Equipos Inscritos ({{ $torneo->equipos->count() }})
                </h3>
            </div>
            <div class="p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($torneo->equipos as $equipo)
                        <div class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 transition">
                            <div class="flex items-start justify-between mb-2">
                                <h4 class="font-semibold text-gray-900">{{ $equipo->nombre }}</h4>
                                @if($equipo->grupo)
                                    <span class="text-xs bg-purple-100 text-purple-800 px-2 py-1 rounded-full">
                                        {{ $equipo->grupo->nombre }}
                                    </span>
                                @endif
                            </div>
                            <div class="text-sm text-gray-600">
                                <p class="flex items-center gap-1 mb-1">
                                    <i class="fas fa-users text-xs"></i>
                                    {{ $equipo->jugadores->count() }} jugadores
                                </p>
                                @if($equipo->jugadores->isNotEmpty())
                                    <ul class="ml-5 text-xs space-y-0.5">
                                        @foreach($equipo->jugadores as $jugador)
                                            <li>• {{ $jugador->nombre }} {{ $jugador->apellido }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Descripción -->
    @if($torneo->descripcion)
        <div class="bg-white rounded-lg shadow p-4 md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                <i class="fas fa-info-circle text-brand-600"></i>
                Descripción
            </h3>
            <p class="text-gray-700 leading-relaxed">{{ $torneo->descripcion }}</p>
        </div>
    @endif

    <!-- Información Adicional -->
    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 md:p-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-3">Información del Sistema</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Creado:</span>
                <span class="text-gray-900 font-medium ml-2">{{ $torneo->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-500">Última actualización:</span>
                <span class="text-gray-900 font-medium ml-2">{{ $torneo->updated_at->format('d/m/Y H:i') }}</span>
            </div>
            <div>
                <span class="text-gray-500">ID del torneo:</span>
                <span class="text-gray-900 font-medium ml-2 font-mono">#{{ $torneo->id }}</span>
            </div>
        </div>
    </div>
</div>
@endsection
