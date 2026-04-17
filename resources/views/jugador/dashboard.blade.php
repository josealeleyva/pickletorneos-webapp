@extends('layouts.jugador')

@section('title', 'Mi Panel')
@section('page-title', 'Mi Panel')

@section('content')
<div class="max-w-7xl mx-auto">

    {{-- Sin perfil vinculado --}}
    @if(!$jugador)
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-yellow-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-yellow-800">Completá tu perfil</p>
                    <p class="text-xs text-yellow-700 mt-1">Para ver tus partidos y torneos, completá tus datos de jugador.</p>
                    <a href="{{ route('jugador.perfil') }}" class="inline-block mt-2 text-xs font-semibold text-yellow-800 underline">Ir a mi perfil</a>
                </div>
            </div>
        </div>
    @elseif($jugador)
        @php
            $camposFaltantes = collect([
                'nombre'           => 'Nombre',
                'apellido'         => 'Apellido',
                'dni'              => 'DNI',
                'fecha_nacimiento' => 'Fecha de nacimiento',
                'genero'           => 'Sexo',
            ])->filter(fn($label, $campo) => empty($jugador->$campo))->values();
        @endphp
        @if($camposFaltantes->isNotEmpty())
            <div class="bg-accent-50 border border-accent-200 rounded-lg p-4 mb-6">
                <div class="flex items-start gap-3">
                    <svg class="w-5 h-5 text-accent-600 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                    </svg>
                    <div class="flex-1">
                        <p class="text-sm font-semibold text-accent-800">Tu perfil está incompleto</p>
                        <p class="text-xs text-accent-700 mt-1">
                            Falta completar:
                            <span class="font-semibold">{{ $camposFaltantes->implode(', ') }}</span>.
                        </p>
                        <p class="text-xs text-accent-600 mt-0.5">Completar estos datos te permite usar el filtro de inscripción en torneos.</p>
                        <a href="{{ route('jugador.perfil') }}" class="inline-block mt-2 text-xs font-semibold text-accent-800 underline">
                            Ir a mi perfil →
                        </a>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Layout de dos columnas en desktop --}}
    <div class="flex flex-col lg:flex-row gap-6">

        {{-- Columna principal: Próximos Partidos --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Próximos Partidos
                </h2>
            </div>

            @if($proximosPartidos->isEmpty())
                <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-8 text-center">
                    <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="text-gray-500 text-sm font-medium">No tenés partidos programados</p>
                    <p class="text-gray-400 text-xs mt-1">Cuando el organizador programe tus partidos, aparecerán aquí.</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($proximosPartidos as $partido)
                        @php
                            $torneo = $partido->equipo1?->torneo ?? $partido->equipo2?->torneo;
                            $esEquipo1 = $jugador && $jugador->equipos->contains($partido->equipo1_id);
                            $miEquipo = $esEquipo1 ? $partido->equipo1 : $partido->equipo2;
                            $rival = $esEquipo1 ? $partido->equipo2 : $partido->equipo1;
                            $diasRestantes = $partido->fecha_hora ? now()->diffInDays($partido->fecha_hora, false) : null;
                        @endphp
                        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition">
                            {{-- Torneo y badge de tiempo --}}
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs font-medium text-brand-600 bg-brand-50 px-2 py-0.5 rounded-full truncate max-w-[60%]">
                                    {{ $torneo?->nombre ?? 'Torneo' }}
                                </span>
                                @if($diasRestantes !== null)
                                    @if($diasRestantes === 0)
                                        <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-0.5 rounded-full">Hoy</span>
                                    @elseif($diasRestantes === 1)
                                        <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-0.5 rounded-full">Mañana</span>
                                    @else
                                        <span class="text-xs text-gray-500">En {{ $diasRestantes }} días</span>
                                    @endif
                                @endif
                            </div>

                            {{-- Equipos --}}
                            <div class="flex items-center gap-3">
                                <div class="flex-1 text-center">
                                    <div class="w-10 h-10 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-1">
                                        <span class="text-xs font-bold text-brand-700">{{ substr($miEquipo?->nombre ?? 'Yo', 0, 2) }}</span>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $miEquipo?->nombre ?? 'Mi equipo' }}</p>
                                    <p class="text-xs text-brand-500 font-medium">Vos</p>
                                </div>

                                <div class="text-center flex-shrink-0">
                                    <span class="text-lg font-bold text-gray-400">VS</span>
                                </div>

                                <div class="flex-1 text-center">
                                    <div class="w-10 h-10 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-1">
                                        <span class="text-xs font-bold text-gray-600">{{ substr($rival?->nombre ?? '?', 0, 2) }}</span>
                                    </div>
                                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $rival?->nombre ?? 'Por definir' }}</p>
                                    <p class="text-xs text-gray-400">Rival</p>
                                </div>
                            </div>

                            {{-- Fecha y cancha --}}
                            <div class="mt-3 pt-3 border-t border-gray-50 flex flex-wrap gap-3 text-xs text-gray-500">
                                @if($partido->fecha_hora)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        {{ $partido->fecha_hora->format('d/m/Y') }} — {{ $partido->fecha_hora->format('H:i') }}hs
                                    </span>
                                @endif
                                @if($partido->cancha)
                                    <span class="flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        </svg>
                                        {{ $partido->cancha->nombre }}
                                    </span>
                                @endif
                            </div>

                            {{-- Acción de resultado (si el partido ya ocurrió y no tiene resultado oficial) --}}
                            @if($partido->fecha_hora && $partido->fecha_hora->isPast() && !$partido->equipo_ganador_id)
                                <div class="mt-3 pt-3 border-t border-gray-50">
                                    @if(!$partido->resultadoTentativo)
                                        <a href="{{ route('jugador.partidos') }}"
                                           class="block text-center text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 py-2 rounded-lg transition">
                                            Cargar resultado →
                                        </a>
                                    @elseif($partido->resultadoTentativo->propuesto_por_equipo_id === $miEquipo?->id)
                                        <p class="text-center text-xs text-gray-400 py-1.5">Esperando confirmación del rival…</p>
                                    @else
                                        <a href="{{ route('jugador.partidos') }}"
                                           class="block text-center text-xs font-semibold text-orange-600 bg-orange-50 hover:bg-orange-100 py-2 rounded-lg transition">
                                            Confirmar resultado →
                                        </a>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Sidebar: Mis Torneos --}}
        <div class="w-full lg:w-72 flex-shrink-0">
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden sticky top-4">
                <div class="p-4 border-b border-gray-100">
                    <h3 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Mis Torneos
                    </h3>
                </div>

                {{-- Tabs --}}
                <div class="flex border-b border-gray-100 text-xs font-semibold">
                    <button onclick="showTab('activos')" id="tab-activos"
                        class="flex-1 py-2.5 text-center transition tab-btn tab-active">
                        Activos
                        @if($torneosActivos->count() > 0)
                            <span class="ml-1 bg-brand-600 text-white text-xs rounded-full px-1.5">{{ $torneosActivos->count() }}</span>
                        @endif
                    </button>
                    <button onclick="showTab('historial')" id="tab-historial"
                        class="flex-1 py-2.5 text-center transition tab-btn text-gray-500">
                        Historial
                    </button>
                    <button onclick="showTab('explorar')" id="tab-explorar"
                        class="flex-1 py-2.5 text-center transition tab-btn text-gray-500">
                        Explorar
                    </button>
                </div>

                {{-- Tab: Activos --}}
                <div id="panel-activos" class="p-3 space-y-2 max-h-80 overflow-y-auto">
                    @forelse($torneosActivos as $torneo)
                        <a href="{{ route('torneos.public', $torneo->id) }}" class="block p-3 rounded-lg hover:bg-brand-50 border border-transparent hover:border-brand-100 transition group">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-brand-100 rounded-lg flex items-center justify-center flex-shrink-0 group-hover:bg-brand-200 transition">
                                    <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-800 truncate">{{ $torneo->nombre }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $torneo->deporte?->nombre }} • {{ $torneo->complejo?->nombre }}</p>
                                </div>
                            </div>
                            <div class="mt-1.5 ml-10">
                                <span class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-50 px-1.5 py-0.5 rounded">
                                    <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                    {{ $torneo->estado === 'en_curso' ? 'En curso' : 'Activo' }}
                                </span>
                            </div>
                        </a>
                    @empty
                        <div class="py-6 text-center">
                            <p class="text-xs text-gray-400">No estás en ningún torneo activo.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Tab: Historial --}}
                <div id="panel-historial" class="p-3 space-y-2 max-h-80 overflow-y-auto hidden">
                    @forelse($torneosHistorial as $torneo)
                        <a href="{{ route('torneos.public', $torneo->id) }}" class="block p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-gray-700 truncate">{{ $torneo->nombre }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $torneo->deporte?->nombre }} • {{ $torneo->complejo?->nombre }}</p>
                                </div>
                            </div>
                        </a>
                    @empty
                        <div class="py-6 text-center">
                            <p class="text-xs text-gray-400">Todavía no participaste en ningún torneo.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Tab: Explorar --}}
                <div id="panel-explorar" class="hidden">
                    <div class="p-3 border-b border-gray-50">
                        <p class="text-xs text-gray-500 text-center">Todos los torneos activos de la plataforma</p>
                    </div>
                    <div class="p-3">
                        <a href="{{ route('jugador.torneos') }}?tab=explorar"
                            class="block w-full text-center text-xs font-semibold text-brand-600 bg-brand-50 hover:bg-brand-100 py-2 rounded-lg transition">
                            Ver todos los torneos →
                        </a>
                    </div>
                </div>

                {{-- Ver todos --}}
                <div class="p-3 border-t border-gray-100">
                    <a href="{{ route('jugador.torneos') }}" class="block text-center text-xs font-medium text-brand-600 hover:text-brand-700">
                        Ver sección completa de torneos →
                    </a>
                </div>
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    function showTab(nombre) {
        const panels = ['activos', 'historial', 'explorar'];
        panels.forEach(p => {
            document.getElementById('panel-' + p).classList.add('hidden');
            const tab = document.getElementById('tab-' + p);
            tab.classList.remove('tab-active', 'text-brand-700', 'border-b-2', 'border-brand-600');
            tab.classList.add('text-gray-500');
        });

        document.getElementById('panel-' + nombre).classList.remove('hidden');
        const activeTab = document.getElementById('tab-' + nombre);
        activeTab.classList.remove('text-gray-500');
        activeTab.classList.add('text-brand-700', 'border-b-2', 'border-brand-600');
    }

    // Inicializar tab activo
    document.addEventListener('DOMContentLoaded', function() {
        showTab('activos');
    });
</script>
<style>
    .tab-active {
        color: #0F6B78;
        border-bottom: 2px solid #147a8a;
    }
</style>
@endpush
@endsection
