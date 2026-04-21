@extends('layouts.jugador')

@section('title', 'Mis Torneos')
@section('page-title', 'Mis Torneos')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Tabs --}}
    <div class="flex gap-1 bg-gray-100 rounded-xl p-1 mb-6" id="tabs-torneos">
        <button onclick="showTab('activos')" id="tab-activos"
            class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition text-center">
            Activos
            @if($torneosActivos->count() > 0)
                <span class="ml-1 bg-brand-600 text-white text-xs rounded-full px-1.5">{{ $torneosActivos->count() }}</span>
            @endif
        </button>
        <button onclick="showTab('historial')" id="tab-historial"
            class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition text-center text-gray-500">
            Historial
            @if($torneosHistorial->count() > 0)
                <span class="ml-1 bg-gray-400 text-white text-xs rounded-full px-1.5">{{ $torneosHistorial->count() }}</span>
            @endif
        </button>
        <button onclick="showTab('explorar')" id="tab-explorar"
            class="flex-1 py-2.5 rounded-lg text-sm font-semibold transition text-center text-gray-500">
            Explorar
        </button>
    </div>

    {{-- Panel: Activos --}}
    <div id="panel-activos">
        @if($torneosActivos->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-10 text-center">
                <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <p class="text-gray-500 font-medium">No estás en ningún torneo activo</p>
                <p class="text-gray-400 text-sm mt-1">Cuando un organizador te inscriba en un torneo, aparecerá aquí.</p>
                <button onclick="showTab('explorar')" class="mt-4 text-sm font-semibold text-brand-600 hover:text-brand-700">
                    Explorar torneos →
                </button>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($torneosActivos as $torneo)
                    <a href="{{ route('torneos.public', $torneo->id) }}"
                        class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md hover:border-brand-200 transition block">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-gray-800 text-sm leading-snug flex-1 pr-2">{{ $torneo->nombre }}</h3>
                            <span class="flex items-center gap-1 text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full flex-shrink-0">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                {{ $torneo->estado === 'en_curso' ? 'En curso' : 'Activo' }}
                            </span>
                        </div>
                        <div class="space-y-1 text-xs text-gray-500">
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ $torneo->deporte?->nombre ?? '—' }}
                            </p>
                            @if($torneo->complejo)
                                <p class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $torneo->complejo->nombre }}
                                </p>
                            @endif
                            @if($torneo->fecha_inicio)
                                <p class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Desde {{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('d/m/Y') }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Panel: Historial --}}
    <div id="panel-historial" class="hidden">
        @if($torneosHistorial->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-10 text-center">
                <p class="text-gray-500 font-medium">Todavía no participaste en ningún torneo</p>
                <p class="text-gray-400 text-sm mt-1">Tus torneos finalizados aparecerán aquí con tu posición final.</p>
            </div>
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                @foreach($torneosHistorial as $torneo)
                    @php $posicion = $posicionesFinal[$torneo->id] ?? 'Participante'; @endphp
                    <a href="{{ route('torneos.public', $torneo->id) }}"
                        class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md transition block">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-gray-700 text-sm leading-snug flex-1 pr-2">{{ $torneo->nombre }}</h3>
                            @if($posicion === 'Campeón')
                                <span class="text-xs font-semibold text-yellow-700 bg-yellow-50 px-2 py-0.5 rounded-full flex-shrink-0">🥇 Campeón</span>
                            @elseif($posicion === '2do puesto')
                                <span class="text-xs font-semibold text-slate-600 bg-slate-100 px-2 py-0.5 rounded-full flex-shrink-0">🥈 2do puesto</span>
                            @elseif($posicion === 'Semifinalista')
                                <span class="text-xs font-semibold text-orange-700 bg-orange-50 px-2 py-0.5 rounded-full flex-shrink-0">🥉 Semifinalista</span>
                            @else
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full flex-shrink-0">Participante</span>
                            @endif
                        </div>
                        <div class="space-y-1 text-xs text-gray-500">
                            <p class="flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                {{ $torneo->deporte?->nombre ?? '—' }}
                            </p>
                            @if($torneo->complejo)
                                <p class="flex items-center gap-1.5">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                                    {{ $torneo->complejo->nombre }}
                                </p>
                            @endif
                        </div>
                    </a>
                @endforeach
            </div>
        @endif
    </div>

    {{-- Panel: Explorar --}}
    <div id="panel-explorar" class="hidden">
        {{-- Filtros --}}
        <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 mb-4">
            <div class="flex flex-col sm:flex-row gap-3 mb-3">
                <input type="text" id="filtro-nombre" placeholder="Buscar por nombre, complejo o categoría..."
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            </div>
            <div class="flex items-center justify-between">
                <label for="filtro-inscribible" class="flex items-center gap-2.5 cursor-pointer select-none">
                    <div class="relative">
                        <input type="checkbox" id="filtro-inscribible" class="sr-only peer">
                        <div class="w-10 h-5 bg-gray-200 peer-checked:bg-brand-600 rounded-full transition-colors duration-200"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow transition-transform duration-200 peer-checked:translate-x-5"></div>
                    </div>
                    <span class="text-sm text-gray-700 font-medium">Solo los que me puedo inscribir</span>
                </label>
                @if($jugador && (!$jugador->genero || !$jugador->fecha_nacimiento))
                    <span id="aviso-perfil-incompleto" class="hidden text-xs text-accent-700 bg-accent-50 border border-accent-200 px-2.5 py-1 rounded-full">
                        Completá tu perfil para usar este filtro
                    </span>
                @endif
            </div>
        </div>

        @if($torneosExplorar->isEmpty())
            <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-10 text-center">
                <p class="text-gray-500 font-medium">No hay torneos activos por el momento</p>
            </div>
        @else
            <div id="sin-resultados-explorar" class="hidden bg-white rounded-lg shadow-sm border border-gray-100 p-10 text-center">
                <p class="text-gray-500 font-medium">Ningún torneo coincide con los filtros</p>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4" id="lista-explorar">
                @foreach($torneosExplorar as $torneo)
                    @php
                        $categoriasData = $torneo->categorias->map(fn($c) => [
                            'nombre'           => $c->nombre,
                            'edad_minima'      => $c->pivot->edad_minima,
                            'edad_maxima'      => $c->pivot->edad_maxima,
                            'genero_permitido' => $c->pivot->genero_permitido,
                        ])->values()->toArray();
                    @endphp
                    <a href="{{ route('torneos.public', $torneo->id) }}"
                        data-deporte="{{ $torneo->deporte_id }}"
                        data-busqueda="{{ strtolower($torneo->nombre . ' ' . ($torneo->complejo?->nombre ?? '') . ' ' . $torneo->categorias->pluck('nombre')->implode(' ')) }}"
                        data-categorias="{{ json_encode($categoriasData) }}"
                        class="torneo-card bg-white rounded-lg shadow-sm border border-gray-100 p-4 hover:shadow-md hover:border-brand-200 transition block">
                        <div class="flex items-start justify-between mb-3">
                            <h3 class="font-semibold text-gray-800 text-sm leading-snug flex-1 pr-2">{{ $torneo->nombre }}</h3>
                            <span class="flex items-center gap-1 text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full flex-shrink-0">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                {{ $torneo->estado === 'en_curso' ? 'En curso' : 'Activo' }}
                            </span>
                        </div>
                        <div class="space-y-1 text-xs text-gray-500">
                            <p>{{ $torneo->deporte?->nombre ?? '—' }} • {{ $torneo->complejo?->nombre ?? '—' }}</p>
                            @if($torneo->fecha_inicio)
                                <p>Desde {{ \Carbon\Carbon::parse($torneo->fecha_inicio)->format('d/m/Y') }}</p>
                            @endif
                        </div>
                        @if($torneo->categorias->isNotEmpty())
                            <div class="mt-3 pt-3 border-t border-gray-100 flex flex-wrap gap-1.5">
                                @foreach($torneo->categorias as $categoria)
                                    <span class="text-xs text-gray-600 font-medium">{{ $categoria->nombre }}:</span>
                                    @include('partials.categoria-restricciones', ['categoria' => $categoria])
                                @endforeach
                            </div>
                        @endif
                    </a>
                @endforeach
            </div>
        @endif
    </div>

</div>

@push('scripts')
<script>
    function showTab(nombre) {
        ['activos', 'historial', 'explorar'].forEach(p => {
            document.getElementById('panel-' + p).classList.add('hidden');
            const tab = document.getElementById('tab-' + p);
            tab.classList.remove('bg-white', 'text-brand-700', 'shadow');
            tab.classList.add('text-gray-500');
        });
        document.getElementById('panel-' + nombre).classList.remove('hidden');
        const activeTab = document.getElementById('tab-' + nombre);
        activeTab.classList.remove('text-gray-500');
        activeTab.classList.add('bg-white', 'text-brand-700', 'shadow');
    }

    // Datos del jugador para el filtro de inscripción
    const jugadorGenero = @json($jugador?->genero);
    const jugadorFechaNacimiento = @json($jugador?->fecha_nacimiento?->format('Y-m-d'));

    function calcularEdad(fechaNacimiento) {
        const hoy = new Date();
        const nacimiento = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - nacimiento.getFullYear();
        const m = hoy.getMonth() - nacimiento.getMonth();
        if (m < 0 || (m === 0 && hoy.getDate() < nacimiento.getDate())) {
            edad--;
        }
        return edad;
    }

    function jugadorCumpleCategoria(categoria) {
        const genero = categoria.genero_permitido;
        const edadMin = categoria.edad_minima;
        const edadMax = categoria.edad_maxima;

        // Verificar género
        if (genero) {
            if (genero === 'mixto') {
                // mixto acepta cualquier género
            } else if (genero !== jugadorGenero) {
                return false;
            }
        }

        // Verificar edad
        if (edadMin || edadMax) {
            const edad = calcularEdad(jugadorFechaNacimiento);
            if (edadMin && edad < edadMin) { return false; }
            if (edadMax && edad > edadMax) { return false; }
        }

        return true;
    }

    function jugadorPuedeInscribirse(card) {
        const categorias = JSON.parse(card.dataset.categorias || '[]');

        // Sin categorías definidas → torneo abierto
        if (categorias.length === 0) { return true; }

        // Basta con que cumpla al menos una categoría
        return categorias.some(jugadorCumpleCategoria);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const tab = new URLSearchParams(window.location.search).get('tab') || 'activos';
        showTab(tab);

        const switchInscribible = document.getElementById('filtro-inscribible');
        const avisoPerfilIncompleto = document.getElementById('aviso-perfil-incompleto');
        const sinResultados = document.getElementById('sin-resultados-explorar');
        const listaExplorar = document.getElementById('lista-explorar');

        function filtrarTorneos() {
            const deporte = document.getElementById('filtro-deporte').value;
            const busqueda = document.getElementById('filtro-nombre').value.toLowerCase().trim();
            const soloInscribibles = switchInscribible?.checked ?? false;

            // Perfil incompleto: mostrar aviso y ocultar todo
            if (soloInscribibles && (!jugadorGenero || !jugadorFechaNacimiento)) {
                avisoPerfilIncompleto?.classList.remove('hidden');
                document.querySelectorAll('.torneo-card').forEach(card => card.classList.add('hidden'));
                if (sinResultados) { sinResultados.classList.remove('hidden'); }
                return;
            } else {
                avisoPerfilIncompleto?.classList.add('hidden');
            }

            let visibles = 0;
            document.querySelectorAll('.torneo-card').forEach(card => {
                const matchDeporte = !deporte || card.dataset.deporte === deporte;
                const matchBusqueda = !busqueda || card.dataset.busqueda.includes(busqueda);
                const matchInscribible = !soloInscribibles || jugadorPuedeInscribirse(card);
                const visible = matchDeporte && matchBusqueda && matchInscribible;
                card.classList.toggle('hidden', !visible);
                if (visible) { visibles++; }
            });

            if (sinResultados) {
                sinResultados.classList.toggle('hidden', visibles > 0);
            }
            if (listaExplorar) {
                listaExplorar.classList.toggle('hidden', visibles === 0);
            }
        }

        document.getElementById('filtro-deporte')?.addEventListener('change', filtrarTorneos);
        document.getElementById('filtro-nombre')?.addEventListener('input', filtrarTorneos);
        switchInscribible?.addEventListener('change', filtrarTorneos);
    });
</script>
@endpush
@endsection
