@extends('layouts.jugador')

@section('title', 'Invitación a torneo')
@section('page-title', 'Invitación')

@section('content')
<div class="max-w-md mx-auto px-4 py-6">

    @php
        $inscripcion = $invitacion->inscripcionEquipo;
        $torneo = $inscripcion->torneo;
        $lider = $inscripcion->lider;
    @endphp

    @if($invitacion->estado !== 'pendiente')
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-6 text-center">
            <p class="text-gray-600 font-medium">
                Esta invitación ya fue
                {{ $invitacion->estado === 'aceptada' ? 'aceptada' : 'rechazada' }}.
            </p>
            <a href="{{ route('torneos.public', $torneo->id) }}"
               class="mt-4 inline-block text-indigo-600 hover:text-indigo-800 text-sm font-medium">
                Ver torneo
            </a>
        </div>
    @elseif($inscripcion->estado === 'cancelada')
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
            <p class="text-yellow-800 font-medium">Esta inscripción fue cancelada.</p>
        </div>
    @else
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-8 text-center">
                <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-white">¡Te invitaron!</h2>
                <p class="text-white/80 text-sm mt-1">{{ $lider->nombre_completo }} quiere jugar con vos</p>
            </div>

            <div class="p-6">
                <div class="space-y-3 mb-6">
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Torneo</span>
                        <span class="font-medium text-gray-900">{{ $torneo->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Deporte</span>
                        <span class="font-medium text-gray-900">{{ $torneo->deporte->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Categoría</span>
                        <span class="font-medium text-gray-900">{{ $inscripcion->categoria->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Complejo</span>
                        <span class="font-medium text-gray-900">{{ $torneo->complejo->nombre }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="text-gray-500 w-24 flex-shrink-0">Fecha</span>
                        <span class="font-medium text-gray-900">
                            {{ $torneo->fecha_inicio?->format('d/m/Y') }}
                            @if($torneo->fecha_fin && $torneo->fecha_fin != $torneo->fecha_inicio)
                                al {{ $torneo->fecha_fin->format('d/m/Y') }}
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Resto del equipo --}}
                <div class="mb-6">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2">Equipo</p>
                    <div class="space-y-2">
                        @foreach($inscripcion->invitaciones as $inv)
                        <div class="flex items-center justify-between text-sm">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-full bg-indigo-100 flex items-center justify-center text-xs font-bold text-indigo-700">
                                    {{ substr($inv->jugador->apellido, 0, 1) }}
                                </div>
                                <span class="{{ $inv->jugador_id === $lider->id ? 'font-semibold' : '' }} text-gray-900">
                                    {{ $inv->jugador->nombre_completo }}
                                    @if($inv->jugador_id === $lider->id) <span class="text-xs text-gray-400">(líder)</span> @endif
                                </span>
                            </div>
                            <span class="text-xs {{ $inv->estado === 'aceptada' ? 'text-green-600' : 'text-yellow-600' }}">
                                {{ $inv->estado === 'aceptada' ? '✓' : 'Pendiente' }}
                            </span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <form action="{{ route('inscripciones.invitacion.rechazar', $invitacion->token) }}" method="POST">
                        @csrf
                        <button type="submit" onclick="return confirm('¿Rechazar la invitación? La inscripción se cancelará.')"
                                class="w-full py-2.5 border border-gray-300 text-gray-700 hover:bg-gray-50 rounded-lg text-sm font-medium transition">
                            Rechazar
                        </button>
                    </form>

                    <form action="{{ route('inscripciones.invitacion.aceptar', $invitacion->token) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition">
                            Aceptar
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
