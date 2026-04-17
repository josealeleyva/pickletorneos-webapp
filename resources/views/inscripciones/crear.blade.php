@extends('layouts.jugador')

@section('title', 'Inscribirse al torneo')
@section('page-title', 'Inscribirse — ' . $torneo->nombre)

@section('content')
<div class="max-w-2xl mx-auto px-4 md:px-0 py-4 md:py-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800 mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-sm text-red-800 mb-4">
            {{ session('error') }}
        </div>
    @endif

    {{-- Info del torneo --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <h2 class="text-lg font-bold text-gray-900">{{ $torneo->nombre }}</h2>
        <p class="text-sm text-gray-500 mt-1">{{ $torneo->deporte->nombre }} · {{ $torneo->complejo->nombre }}</p>
    </div>

    @if(!isset($inscripcion))
    {{-- PASO 1: Seleccionar categoría --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-4">
        <h3 class="text-base font-semibold text-gray-900 mb-4">Paso 1: Seleccioná la categoría</h3>

        <form action="{{ route('torneos.inscripciones.store', $torneo) }}" method="POST">
            @csrf

            <div class="space-y-3 mb-4">
                @foreach($categorias as $cat)
                @php
                    $info = $elegibilidad[$cat->id] ?? ['elegible' => true, 'motivo' => null];
                    $esElegible = $info['elegible'];
                    $motivo = $info['motivo'];
                @endphp
                @if($esElegible)
                <label class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg cursor-pointer hover:border-brand-400 has-[:checked]:border-brand-500 has-[:checked]:bg-brand-50 transition">
                    <input type="radio" name="categoria_id" value="{{ $cat->id }}" class="mt-1" required>
                    <div>
                        <div class="font-medium text-gray-900 text-sm">{{ $cat->nombre }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            @if($cat->pivot->edad_minima || $cat->pivot->edad_maxima)
                                Edad:
                                @if($cat->pivot->edad_minima) +{{ $cat->pivot->edad_minima }} @endif
                                @if($cat->pivot->edad_maxima) hasta {{ $cat->pivot->edad_maxima }} @endif
                                ·
                            @endif
                            @if($cat->pivot->genero_permitido)
                                {{ ucfirst($cat->pivot->genero_permitido) }} ·
                            @endif
                            {{ $cat->pivot->cupos_categoria ?? '?' }} cupos
                        </div>
                    </div>
                </label>
                @else
                <div class="flex items-start gap-3 p-3 border border-gray-200 rounded-lg bg-gray-50 opacity-60 cursor-not-allowed">
                    <input type="radio" name="categoria_id" value="{{ $cat->id }}" class="mt-1" disabled>
                    <div>
                        <div class="font-medium text-gray-500 text-sm">{{ $cat->nombre }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">
                            @if($cat->pivot->edad_minima || $cat->pivot->edad_maxima)
                                Edad:
                                @if($cat->pivot->edad_minima) +{{ $cat->pivot->edad_minima }} @endif
                                @if($cat->pivot->edad_maxima) hasta {{ $cat->pivot->edad_maxima }} @endif
                                ·
                            @endif
                            @if($cat->pivot->genero_permitido)
                                {{ ucfirst($cat->pivot->genero_permitido) }} ·
                            @endif
                            {{ $cat->pivot->cupos_categoria ?? '?' }} cupos
                        </div>
                        <div class="mt-1 text-xs text-red-500 font-medium flex items-center gap-1">
                            <svg class="w-3 h-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            No cumplís los requisitos: {{ $motivo }}
                        </div>
                    </div>
                </div>
                @endif
                @endforeach
            </div>

            @if($requiereNombre)
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nombre del equipo</label>
                <input type="text" name="nombre_equipo" required
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500"
                       placeholder="Ej: Los Campeones">
            </div>
            @endif

            <button type="submit"
                    class="w-full bg-brand-600 hover:bg-brand-700 text-white font-medium py-2.5 px-4 rounded-lg text-sm transition">
                Continuar e invitar compañeros
            </button>
        </form>
    </div>

    @else
    {{-- PASO 2: Invitar compañeros --}}
    <div class="bg-white rounded-lg shadow-sm p-4 md:p-6 mb-4">
        <h3 class="text-base font-semibold text-gray-900 mb-1">Paso 2: Invitá a tus compañeros</h3>

        @php
            $totalInvitados = $inscripcion->invitaciones->count();
            $minutosRestantes = $inscripcion->expires_at ? max(0, now()->diffInMinutes($inscripcion->expires_at, false)) : 0;
        @endphp

        <p class="text-sm text-gray-500 mb-4">
            Necesitás {{ $maxJugadores }} jugadores en total · Tiempo restante:
            <span class="font-semibold text-orange-600">{{ $minutosRestantes }} min</span>
        </p>

        {{-- Jugadores ya invitados --}}
        <div class="space-y-2 mb-4">
            @foreach($inscripcion->invitaciones as $inv)
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700">
                        {{ substr($inv->jugador->apellido, 0, 1) }}
                    </div>
                    <span class="text-sm font-medium text-gray-900">{{ $inv->jugador->nombre_completo }}</span>
                </div>
                <span class="text-xs px-2 py-0.5 rounded-full
                    {{ $inv->estado === 'aceptada' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    {{ $inv->estado === 'aceptada' ? 'Confirmado' : 'Pendiente' }}
                </span>
            </div>
            @endforeach
        </div>

        @if($totalInvitados < $maxJugadores)
        {{-- Buscador de jugadores --}}
        <div x-data="buscadorJugadores({{ $torneo->id }}, {{ $inscripcion->categoria_id }})" class="border-t pt-4">
            <label class="block text-sm font-medium text-gray-700 mb-2">Buscar jugador</label>
            <div class="relative">
                <input type="text" x-model="query" @input.debounce.400ms="buscar()"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 pr-8"
                       placeholder="Nombre, apellido, email o teléfono (mín. 2 caracteres)">
                <div x-show="cargando" class="absolute right-2 top-2.5">
                    <svg class="animate-spin h-4 w-4 text-brand-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                </div>

                <div x-show="resultados.length > 0" class="absolute z-10 mt-1 w-full bg-white border border-gray-200 rounded-lg shadow-lg">
                    <template x-for="j in resultados" :key="j.id">
                        <button type="button" @click="seleccionar(j)"
                                class="w-full flex items-center gap-2 px-3 py-2 hover:bg-gray-50 text-left text-sm">
                            <div class="w-7 h-7 rounded-full bg-brand-100 flex items-center justify-center text-xs font-bold text-brand-700 flex-shrink-0"
                                 x-text="j.nombre_completo.charAt(0)"></div>
                            <span x-text="j.nombre_completo"></span>
                        </button>
                    </template>
                </div>

                <p x-show="error" x-text="error" class="mt-1 text-xs text-red-600"></p>
            </div>

            <div x-show="seleccionado" class="mt-3 p-3 bg-brand-50 rounded-lg flex items-center justify-between">
                <span class="text-sm font-medium text-brand-900" x-text="seleccionado?.nombre_completo"></span>
                <form :action="`/inscripciones/{{ $inscripcion->id }}/invitar`" method="POST">
                    @csrf
                    <input type="hidden" name="jugador_id" :value="seleccionado?.id">
                    <button type="submit" class="text-xs bg-brand-600 hover:bg-brand-700 text-white px-3 py-1.5 rounded-lg font-medium transition">
                        Enviar invitación
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Cancelar --}}
        <form action="{{ route('inscripciones.cancelar', $inscripcion) }}" method="POST" class="mt-4">
            @csrf
            @method('DELETE')
            <button type="submit" onclick="return confirm('¿Cancelar la inscripción?')"
                    class="w-full text-sm text-red-600 hover:text-red-800 py-2 font-medium transition">
                Cancelar inscripción
            </button>
        </form>
    </div>
    @endif

</div>

@push('scripts')
<script>
function buscadorJugadores(torneoId, categoriaId) {
    return {
        query: '',
        resultados: [],
        seleccionado: null,
        cargando: false,
        error: null,
        async buscar() {
            if (this.query.length < 2) { this.resultados = []; this.error = null; return; }
            this.cargando = true;
            this.error = null;
            try {
                const res = await fetch(
                    `/torneos/${torneoId}/inscribirse/buscar?categoria_id=${categoriaId}&q=${encodeURIComponent(this.query)}`,
                    { headers: { 'Accept': 'application/json' } }
                );
                if (!res.ok) {
                    this.resultados = [];
                    this.error = 'Error al buscar jugadores. Intentá de nuevo.';
                    return;
                }
                this.resultados = await res.json();
            } catch (e) {
                this.resultados = [];
                this.error = 'Error de conexión. Verificá tu internet.';
            } finally {
                this.cargando = false;
            }
        },
        seleccionar(j) {
            this.seleccionado = j;
            this.resultados = [];
            this.error = null;
            this.query = j.nombre_completo;
        }
    }
}
</script>
@endpush
@endsection
