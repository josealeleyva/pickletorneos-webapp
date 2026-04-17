@extends('layouts.jugador')

@section('title', 'Mis Partidos')
@section('page-title', 'Mis Partidos')

@section('content')
<div class="max-w-4xl mx-auto px-4 md:px-0 py-4 md:py-6">

    {{-- ===================== SECCIÓN 1: PENDIENTES DE CONFIRMACIÓN ===================== --}}
    @if($pendientesConfirmacion->count() > 0)
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 flex items-center gap-2">
            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
            Pendiente de tu confirmación
        </h2>

        @foreach($pendientesConfirmacion as $tentativo)
            @php
                $partido = $tentativo->partido;
                $torneo = $partido->equipo1->torneo;
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-orange-200 p-4 mb-4"
                 x-data="formularioResultado()">

                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-2 mb-3">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $torneo->nombre }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $partido->equipo1->nombre }} vs {{ $partido->equipo2->nombre }}
                        </p>
                        <p class="text-xs text-orange-600 mt-1">
                            Propuesto por <strong>{{ $tentativo->propuestoPorEquipo->nombre }}</strong>
                        </p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <span class="text-xs text-gray-400">
                            {{ $partido->fecha_hora?->format('d/m/Y H:i') }}hs
                        </span>
                    </div>
                </div>

                {{-- Resultado propuesto --}}
                <div class="bg-gray-50 rounded-lg p-3 mb-3">
                    <p class="text-xs text-gray-500 mb-2">Resultado propuesto:</p>
                    <div class="flex items-center gap-3 text-sm">
                        <span class="font-medium text-gray-800">{{ $partido->equipo1->nombre }}</span>
                        <div class="flex gap-1">
                            @foreach($tentativo->juegos as $juego)
                                <span class="bg-white border border-gray-200 rounded px-2 py-0.5 text-xs font-mono font-bold">
                                    {{ $juego['juego_equipo1'] }}-{{ $juego['juego_equipo2'] }}
                                </span>
                            @endforeach
                        </div>
                        <span class="font-medium text-gray-800">{{ $partido->equipo2->nombre }}</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        Total: {{ $tentativo->sets_equipo1 }} - {{ $tentativo->sets_equipo2 }}
                        @if($tentativo->equipoGanador)
                            · Gana <strong>{{ $tentativo->equipoGanador->nombre }}</strong>
                        @else
                            · Empate
                        @endif
                    </p>
                </div>

                {{-- Botones de acción --}}
                <div x-show="!modificando" class="flex gap-2">
                    <form action="{{ route('jugador.resultados.confirmar', $tentativo) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit"
                                onclick="return confirm('¿Confirmás este resultado?')"
                                class="w-full bg-brand-600 hover:bg-brand-700 text-white text-sm font-medium py-2 rounded-lg transition">
                            Confirmar
                        </button>
                    </form>
                    <button @click="modificando = true"
                            class="flex-1 bg-white hover:bg-gray-50 border border-gray-300 text-gray-700 text-sm font-medium py-2 rounded-lg transition">
                        Modificar
                    </button>
                </div>

                {{-- Formulario inline de modificación --}}
                <div x-show="modificando" x-cloak>
                    <p class="text-xs font-semibold text-gray-600 mb-2">Cargá el resultado correcto:</p>

                    {{-- Juegos cargados --}}
                    <div class="space-y-1 mb-3">
                        <template x-for="(j, i) in juegos" :key="i">
                            <div class="flex items-center gap-2 bg-gray-50 rounded px-3 py-2">
                                <span class="text-xs text-gray-500 w-12" x-text="'Juego ' + (i+1)"></span>
                                <span class="font-bold text-sm flex-1 text-center" x-text="j.juego_equipo1 + ' - ' + j.juego_equipo2"></span>
                                <button type="button" @click="eliminarJuego(i)" class="text-red-500 hover:text-red-700 text-xs">✕</button>
                            </div>
                        </template>
                    </div>

                    {{-- Totales --}}
                    <div x-show="juegos.length > 0" class="bg-brand-50 rounded px-3 py-2 mb-3 text-center text-sm">
                        Total: <strong x-text="totalEquipo1"></strong> - <strong x-text="totalEquipo2"></strong>
                    </div>

                    {{-- Agregar juego --}}
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <input type="number" x-model="juego1" min="0" placeholder="{{ $partido->equipo1->nombre }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <input type="number" x-model="juego2" min="0" placeholder="{{ $partido->equipo2->nombre }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                    <button @click="agregarJuego()"
                            class="w-full text-sm border border-brand-300 text-brand-600 hover:bg-brand-50 py-1.5 rounded-lg mb-3 transition">
                        + Agregar set/juego
                    </button>

                    {{-- Submit --}}
                    <form action="{{ route('jugador.resultados.modificar', $tentativo) }}" method="POST"
                          @submit.prevent="submitForm($el)">
                        @csrf
                        <div class="flex gap-2">
                            <button type="button" @click="modificando = false; resetForm()"
                                    class="flex-1 text-sm text-gray-500 hover:text-gray-700 py-2 border border-gray-200 rounded-lg transition">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="juegos.length === 0"
                                    class="flex-1 bg-orange-500 hover:bg-orange-600 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium py-2 rounded-lg transition">
                                Enviar modificación
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endforeach
    @endif

    {{-- ===================== SECCIÓN 2: ESPERANDO RIVAL ===================== --}}
    @if($esperandoRival->count() > 0)
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-yellow-400 rounded-full"></span>
            Esperando confirmación del rival
        </h2>

        @foreach($esperandoRival as $tentativo)
            @php $partido = $tentativo->partido; @endphp
            <div class="bg-white rounded-lg shadow-sm border border-yellow-200 p-4 mb-3">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $partido->equipo1->torneo->nombre }}</p>
                        <p class="text-xs text-gray-500">{{ $partido->equipo1->nombre }} vs {{ $partido->equipo2->nombre }}</p>
                        <div class="flex gap-1 mt-2">
                            @foreach($tentativo->juegos as $juego)
                                <span class="bg-gray-100 rounded px-2 py-0.5 text-xs font-mono font-bold">
                                    {{ $juego['juego_equipo1'] }}-{{ $juego['juego_equipo2'] }}
                                </span>
                            @endforeach
                        </div>
                        <p class="text-xs text-gray-400 mt-1">
                            Total {{ $tentativo->sets_equipo1 }}-{{ $tentativo->sets_equipo2 }}
                        </p>
                    </div>
                    <span class="text-xs bg-yellow-100 text-yellow-700 font-semibold px-2 py-1 rounded-full flex-shrink-0">
                        Esperando rival
                    </span>
                </div>
            </div>
        @endforeach
    @endif

    {{-- ===================== SECCIÓN 3: SIN RESULTADO ===================== --}}
    @if($sinResultado->count() > 0)
        <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3 mt-4 flex items-center gap-2">
            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
            Sin resultado
        </h2>

        @foreach($sinResultado as $partido)
            @php
                $rivalEquipo = $equipoIds->contains($partido->equipo1_id) ? $partido->equipo2 : $partido->equipo1;
                $rivalTieneUsuarios = $rivalEquipo->jugadores->contains(fn($j) => $j->user_id !== null);
            @endphp
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 mb-3"
                 x-data="formularioResultado()">

                <div class="flex items-start justify-between mb-3">
                    <div>
                        <p class="font-semibold text-gray-900 text-sm">{{ $partido->equipo1->torneo->nombre }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $partido->equipo1->nombre }} vs {{ $partido->equipo2->nombre }}
                        </p>
                        <p class="text-xs text-gray-400 mt-0.5">
                            Jugado el {{ $partido->fecha_hora->format('d/m/Y') }} a las {{ $partido->fecha_hora->format('H:i') }}hs
                        </p>
                    </div>
                </div>

                @if(!$rivalTieneUsuarios)
                    <div class="flex items-start gap-2 bg-accent-50 border border-accent-200 rounded-lg px-3 py-2 text-xs text-accent-700">
                        <svg class="w-4 h-4 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"/>
                        </svg>
                        <span>El equipo rival no tiene jugadores registrados en la plataforma. El organizador del torneo debe cargar el resultado manualmente.</span>
                    </div>
                @else
                <div x-show="!cargando">
                    <button @click="cargando = true"
                            class="w-full text-sm text-brand-600 border border-brand-300 hover:bg-brand-50 py-2 rounded-lg transition font-medium">
                        Cargar resultado
                    </button>
                </div>

                <div x-show="cargando" x-cloak>
                    {{-- Juegos cargados --}}
                    <div class="space-y-1 mb-3">
                        <template x-for="(j, i) in juegos" :key="i">
                            <div class="flex items-center gap-2 bg-gray-50 rounded px-3 py-2">
                                <span class="text-xs text-gray-500 w-12" x-text="'Juego ' + (i+1)"></span>
                                <span class="font-bold text-sm flex-1 text-center" x-text="j.juego_equipo1 + ' - ' + j.juego_equipo2"></span>
                                <button type="button" @click="eliminarJuego(i)" class="text-red-500 hover:text-red-700 text-xs">✕</button>
                            </div>
                        </template>
                    </div>

                    <div x-show="juegos.length > 0" class="bg-brand-50 rounded px-3 py-2 mb-3 text-center text-sm">
                        Total: <strong x-text="totalEquipo1"></strong> - <strong x-text="totalEquipo2"></strong>
                    </div>

                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <input type="number" x-model="juego1" min="0" placeholder="{{ $partido->equipo1->nombre }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-brand-500 focus:outline-none">
                        <input type="number" x-model="juego2" min="0" placeholder="{{ $partido->equipo2->nombre }}"
                               class="border border-gray-300 rounded-lg px-3 py-2 text-sm text-center focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                    <button @click="agregarJuego()"
                            class="w-full text-sm border border-brand-300 text-brand-600 hover:bg-brand-50 py-1.5 rounded-lg mb-3 transition">
                        + Agregar set/juego
                    </button>

                    <form action="{{ route('jugador.partidos.resultado.store', $partido) }}" method="POST"
                          @submit.prevent="submitForm($el)">
                        @csrf
                        <div class="flex gap-2">
                            <button type="button" @click="cargando = false; resetForm()"
                                    class="flex-1 text-sm text-gray-500 border border-gray-200 hover:bg-gray-50 py-2 rounded-lg transition">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="juegos.length === 0"
                                    class="flex-1 bg-brand-600 hover:bg-brand-700 disabled:opacity-50 disabled:cursor-not-allowed text-white text-sm font-medium py-2 rounded-lg transition">
                                Proponer resultado
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        @endforeach
    @endif

    {{-- Estado vacío global --}}
    @if($pendientesConfirmacion->isEmpty() && $esperandoRival->isEmpty() && $sinResultado->isEmpty())
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-10 text-center">
            <div class="w-14 h-14 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <p class="text-gray-500 text-sm font-medium">Todo al día</p>
            <p class="text-gray-400 text-xs mt-1">No tenés partidos pendientes de resultado.</p>
        </div>
    @endif

</div>

@push('scripts')
<script>
function formularioResultado() {
    return {
        cargando: false,
        modificando: false,
        juegos: [],
        juego1: '',
        juego2: '',
        totalEquipo1: 0,
        totalEquipo2: 0,

        agregarJuego() {
            const j1 = parseInt(this.juego1);
            const j2 = parseInt(this.juego2);
            if (isNaN(j1) || isNaN(j2) || this.juego1 === '' || this.juego2 === '') {
                return;
            }
            this.juegos.push({ juego_equipo1: j1, juego_equipo2: j2 });
            this.totalEquipo1 += j1;
            this.totalEquipo2 += j2;
            this.juego1 = '';
            this.juego2 = '';
        },

        eliminarJuego(index) {
            const j = this.juegos[index];
            this.totalEquipo1 -= j.juego_equipo1;
            this.totalEquipo2 -= j.juego_equipo2;
            this.juegos.splice(index, 1);
        },

        resetForm() {
            this.juegos = [];
            this.juego1 = '';
            this.juego2 = '';
            this.totalEquipo1 = 0;
            this.totalEquipo2 = 0;
        },

        submitForm(formEl) {
            if (this.juegos.length === 0) {
                alert('Agregá al menos un juego.');
                return;
            }
            // Limpiar hidden inputs previos
            formEl.querySelectorAll('input[name^="juegos"]').forEach(el => el.remove());
            // Generar hidden inputs para el array de juegos
            this.juegos.forEach((j, i) => {
                const i1 = document.createElement('input');
                i1.type = 'hidden';
                i1.name = `juegos[${i}][juego_equipo1]`;
                i1.value = j.juego_equipo1;
                formEl.appendChild(i1);
                const i2 = document.createElement('input');
                i2.type = 'hidden';
                i2.name = `juegos[${i}][juego_equipo2]`;
                i2.value = j.juego_equipo2;
                formEl.appendChild(i2);
            });
            formEl.submit();
        }
    };
}
</script>
@push('styles')
<style>[x-cloak] { display: none !important; }</style>
@endpush
@endpush
@endsection
