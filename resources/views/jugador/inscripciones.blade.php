@extends('layouts.jugador')

@section('title', 'Mis Inscripciones')
@section('page-title', 'Mis Inscripciones')

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-0 py-4 md:py-6" x-data="{ tab: 'recibidas' }">

    {{-- Tabs --}}
    <div class="flex border-b border-gray-200 mb-6">
        <button @click="tab = 'recibidas'"
                :class="tab === 'recibidas' ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm transition flex items-center gap-2">
            Invitaciones recibidas
            @if($invitacionesPendientes->count() > 0)
                <span class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $invitacionesPendientes->count() }}
                </span>
            @endif
        </button>
        <button @click="tab = 'lideradas'"
                :class="tab === 'lideradas' ? 'border-b-2 border-indigo-600 text-indigo-600 font-semibold' : 'text-gray-500 hover:text-gray-700'"
                class="px-4 py-3 text-sm transition flex items-center gap-2">
            Inscripciones que lidero
            @if($inscripcionesPendientes->count() > 0)
                <span class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">
                    {{ $inscripcionesPendientes->count() }}
                </span>
            @endif
        </button>
    </div>

    {{-- ===================== TAB 1: INVITACIONES RECIBIDAS ===================== --}}
    <div x-show="tab === 'recibidas'" x-cloak>

        {{-- Pendientes --}}
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Pendientes</h3>

        @forelse($invitacionesPendientes as $inv)
            @php $insc = $inv->inscripcionEquipo; @endphp
            <div class="bg-white rounded-lg shadow-sm border border-indigo-200 p-4 mb-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $insc->torneo?->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $insc->categoria?->nombre }}
                            · Invitado por <span class="font-medium">{{ $insc->lider?->nombre_completo }}</span>
                        </p>
                        @if($insc->expires_at)
                            <p class="text-xs text-orange-500 mt-1">
                                Expira {{ $insc->expires_at->diffForHumans() }}
                            </p>
                        @endif
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <form action="{{ route('inscripciones.invitacion.aceptar', $inv->token) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
                                Aceptar
                            </button>
                        </form>
                        <form action="{{ route('inscripciones.invitacion.rechazar', $inv->token) }}" method="POST">
                            @csrf
                            <button type="submit"
                                    onclick="return confirm('¿Rechazar la invitación? Se cancelará la inscripción de todo el equipo.')"
                                    class="bg-white hover:bg-red-50 text-red-600 border border-red-300 text-sm font-medium px-4 py-2 rounded-lg transition">
                                Rechazar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-50 rounded-lg p-6 text-center text-sm text-gray-400 mb-6">
                No tenés invitaciones pendientes
            </div>
        @endforelse

        {{-- Historial --}}
        @if($historialInvitaciones->count() > 0)
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-6">Historial</h3>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                @foreach($historialInvitaciones as $inv)
                    @php $insc = $inv->inscripcionEquipo; @endphp
                    <div class="flex items-center justify-between px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800">{{ $insc->torneo?->nombre }}</p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ $insc->categoria?->nombre }}
                                @if($inv->respondido_at)
                                    · {{ $inv->respondido_at->format('d/m/Y') }}
                                @endif
                            </p>
                        </div>
                        <span class="text-xs font-semibold px-2 py-1 rounded-full flex-shrink-0
                            {{ $inv->estado === 'aceptada' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                            {{ $inv->estado === 'aceptada' ? 'Aceptada' : 'Rechazada' }}
                        </span>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

    {{-- ===================== TAB 2: INSCRIPCIONES QUE LIDERO ===================== --}}
    <div x-show="tab === 'lideradas'" x-cloak>

        {{-- Pendientes --}}
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Pendientes de confirmar</h3>

        @forelse($inscripcionesPendientes as $insc)
            @php
                $totalInvitados = $insc->invitaciones->count();
                $confirmados = $insc->invitaciones->where('estado', 'aceptada')->count();
                $minutosRestantes = $insc->expires_at ? max(0, now()->diffInMinutes($insc->expires_at, false)) : 0;
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-4 mb-3">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $insc->torneo->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">{{ $insc->categoria->nombre }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            <span class="font-medium text-indigo-700">{{ $confirmados }}/{{ $totalInvitados }}</span>
                            jugadores confirmados
                        </p>
                        @if($minutosRestantes > 0)
                            <p class="text-xs text-orange-500 mt-0.5">
                                {{ $minutosRestantes }} min restantes
                            </p>
                        @else
                            <p class="text-xs text-red-500 mt-0.5">Expirada</p>
                        @endif
                    </div>
                    <div class="flex gap-2 flex-shrink-0">
                        <a href="{{ route('inscripciones.invitar', $insc) }}"
                           class="bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition text-center">
                            Gestionar equipo
                        </a>
                        <form action="{{ route('inscripciones.cancelar', $insc) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('¿Cancelar la inscripción? Se notificará a todos los jugadores invitados.')"
                                    class="bg-white hover:bg-red-50 text-red-600 border border-red-300 text-sm font-medium px-4 py-2 rounded-lg transition">
                                Cancelar
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-gray-50 rounded-lg p-6 text-center text-sm text-gray-400 mb-6">
                No liderás ninguna inscripción activa
            </div>
        @endforelse

        {{-- Confirmadas --}}
        @if($inscripcionesConfirmadas->count() > 0)
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-6">Equipos confirmados</h3>
            <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                @foreach($inscripcionesConfirmadas as $insc)
                    <div class="flex items-center justify-between px-4 py-3 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                        <div>
                            <p class="text-sm font-medium text-gray-800">
                                {{ $insc->equipo?->nombre ?? $insc->torneo->nombre }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $insc->categoria->nombre }}</p>
                        </div>
                        <a href="{{ route('torneos.public', $insc->torneo_id) }}"
                           class="text-xs text-indigo-600 hover:text-indigo-800 font-medium flex-shrink-0">
                            Ver torneo →
                        </a>
                    </div>
                @endforeach
            </div>
        @endif

    </div>

</div>

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush
@endsection
