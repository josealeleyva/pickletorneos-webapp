@extends('layouts.dashboard')

@section('title', 'Mis Referidos')

@section('page-title', 'Mis Referidos')

@section('content')
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Programa de Referidos</h1>
        <p class="text-gray-600 mt-2">Invita a otros organizadores y gana torneos gratis</p>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <!-- Total Referidos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Referidos</p>
                    <p class="text-3xl font-bold text-gray-900 mt-2">{{ $stats['total_referidos'] }}</p>
                </div>
                <div class="bg-brand-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Referidos Activos -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Activos</p>
                    <p class="text-3xl font-bold text-green-600 mt-2">{{ $stats['referidos_activos'] }}</p>
                </div>
                <div class="bg-green-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Pendientes -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Pendientes</p>
                    <p class="text-3xl font-bold text-yellow-600 mt-2">{{ $stats['referidos_pendientes'] }}</p>
                </div>
                <div class="bg-yellow-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Créditos Disponibles -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Torneos Gratis</p>
                    <p class="text-3xl font-bold text-purple-600 mt-2">{{ $stats['creditos_disponibles'] }}</p>
                </div>
                <div class="bg-purple-100 rounded-full p-3">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Código de Referido -->
    <div class="bg-gradient-to-r from-brand-600 to-purple-600 rounded-lg shadow-lg p-8 mb-8 text-white">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div>
                <h2 class="text-2xl font-bold mb-4">Tu Código de Referido</h2>
                <p class="text-brand-100 mb-6">Comparte este código con otros organizadores y gana 1 torneo gratis por cada uno que se active.</p>

                <!-- Código -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm text-brand-100 mb-1">Tu código único:</p>
                            <p class="text-3xl font-mono font-bold tracking-wider">{{ $stats['codigo'] }}</p>
                        </div>
                        <button onclick="copiarCodigo()" class="bg-white text-brand-600 px-4 py-2 rounded-lg font-semibold hover:bg-brand-50 transition">
                            Copiar
                        </button>
                    </div>
                </div>

                <!-- Beneficios -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    @php
                        $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);
                        $porcentajeDescuento = \App\Models\ConfiguracionSistema::get('porcentaje_descuento_referido', 20);
                        $montoDescuento = $precioTorneo * ($porcentajeDescuento / 100);
                    @endphp
                    <p class="text-sm font-semibold mb-2">Beneficios para el referido:</p>
                    <p class="text-brand-100">✓ {{ $porcentajeDescuento }}% de descuento en su primer torneo pago (${{ number_format($montoDescuento, 0, ',', '.') }} de ahorro)</p>
                </div>
            </div>

            <div>
                <h3 class="text-xl font-bold mb-4">Compartir por:</h3>
                <div class="space-y-3">
                    <!-- WhatsApp -->
                    <a href="https://wa.me/?text={{ urlencode('¡Únete a PickleTorneos! Usa mi código de referido ' . $stats['codigo'] . ' y obtén ' . $porcentajeDescuento . '% de descuento en tu primer torneo. Regístrate aquí: ' . route('referidos.invitacion', $stats['codigo'])) }}"
                       target="_blank"
                       class="flex items-center gap-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 transition rounded-lg p-4">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
                        </svg>
                        <span class="font-semibold">WhatsApp</span>
                    </a>

                    <!-- Email -->
                    <a href="mailto:?subject={{ urlencode('Únete a PickleTorneos') }}&body={{ urlencode('Hola! Te invito a unirte a PickleTorneos, una plataforma para gestionar torneos deportivos. Usa mi código de referido ' . $stats['codigo'] . ' para obtener ' . $porcentajeDescuento . '% de descuento en tu primer torneo. Regístrate aquí: ' . route('referidos.invitacion', $stats['codigo'])) }}"
                       class="flex items-center gap-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 transition rounded-lg p-4">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <span class="font-semibold">Email</span>
                    </a>

                    <!-- Copiar Link -->
                    <button onclick="copiarLink()" class="w-full flex items-center gap-3 bg-white/10 backdrop-blur-sm hover:bg-white/20 transition rounded-lg p-4 text-left">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                        <span class="font-semibold">Copiar enlace de invitación</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs: Referidos y Créditos -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="border-b border-gray-200">
            <nav class="flex -mb-px">
                <button onclick="cambiarTab('referidos')" id="tab-referidos" class="tab-button active w-1/2 py-4 px-6 text-center border-b-2 font-medium text-sm">
                    Mis Referidos ({{ $stats['total_referidos'] }})
                </button>
                <button onclick="cambiarTab('creditos')" id="tab-creditos" class="tab-button w-1/2 py-4 px-6 text-center border-b-2 font-medium text-sm">
                    Mis Créditos ({{ $stats['creditos_disponibles'] }})
                </button>
            </nav>
        </div>

        <!-- Panel Referidos -->
        <div id="panel-referidos" class="tab-panel p-6">
            @if($referidos->count() > 0)
                <div class="space-y-4">
                    @foreach($referidos as $referido)
                        <div class="border border-gray-200 rounded-lg p-4 hover:border-brand-300 transition">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="bg-brand-100 rounded-full p-3">
                                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900">{{ $referido->referido->name }} {{ $referido->referido->apellido }}</h4>
                                        <p class="text-sm text-gray-600">{{ $referido->referido->email }}</p>
                                        @if($referido->referido->organizacion)
                                            <p class="text-xs text-gray-500">{{ $referido->referido->organizacion }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($referido->estado === 'activo')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Activo
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">Activado el {{ $referido->fecha_activacion->format('d/m/Y') }}</p>
                                    @elseif($referido->estado === 'pendiente')
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            Pendiente
                                        </span>
                                        <p class="text-xs text-gray-500 mt-1">Registrado el {{ $referido->fecha_registro->format('d/m/Y') }}</p>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                            {{ ucfirst($referido->estado) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aún no tienes referidos</h3>
                    <p class="text-gray-600 mb-6">Comparte tu código con otros organizadores y comienza a ganar torneos gratis.</p>
                    <button onclick="copiarCodigo()" class="inline-flex items-center px-6 py-3 bg-brand-600 text-white rounded-lg font-semibold hover:bg-brand-700 transition">
                        Copiar mi código
                    </button>
                </div>
            @endif
        </div>

        <!-- Panel Créditos -->
        <div id="panel-creditos" class="tab-panel hidden p-6">
            @if($creditos->count() > 0)
                <div class="space-y-4">
                    @foreach($creditos as $credito)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-semibold text-gray-900">Torneo Gratis - ${{ number_format($credito->monto, 0, ',', '.') }}</h4>
                                        @if($credito->estado === 'disponible')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Disponible
                                            </span>
                                        @elseif($credito->estado === 'usado')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Usado
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Expirado
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-600 mt-1">
                                        @if($credito->referido_id)
                                            Por referir a {{ $credito->referido->name }} {{ $credito->referido->apellido }}
                                        @else
                                            Crédito otorgado por administrador
                                        @endif
                                    </p>
                                    @if($credito->notas)
                                        <p class="text-xs text-gray-500 mt-1 italic">{{ $credito->notas }}</p>
                                    @endif
                                    <p class="text-xs text-gray-500 mt-1">
                                        @if($credito->estado === 'disponible')
                                            Válido hasta {{ $credito->fecha_vencimiento->format('d/m/Y') }}
                                        @elseif($credito->estado === 'usado')
                                            Usado el {{ $credito->fecha_uso->format('d/m/Y') }}
                                        @endif
                                    </p>
                                </div>
                                @if($credito->estado === 'disponible')
                                    <a href="{{ route('torneos.create') }}" class="inline-flex items-center px-4 py-2 bg-brand-600 text-white rounded-lg font-semibold hover:bg-brand-700 transition text-sm">
                                        Usar ahora
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">No tienes créditos disponibles</h3>
                    <p class="text-gray-600">Ganarás créditos cuando tus referidos activen su cuenta pagando su primer torneo.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function copiarCodigo() {
    const codigo = '{{ $stats["codigo"] }}';
    navigator.clipboard.writeText(codigo).then(() => {
        alert('¡Código copiado al portapapeles!');
    });
}

function copiarLink() {
    const link = '{{ route("referidos.invitacion", $stats["codigo"]) }}';
    navigator.clipboard.writeText(link).then(() => {
        alert('¡Enlace copiado al portapapeles!');
    });
}

function cambiarTab(tab) {
    // Ocultar todos los paneles
    document.querySelectorAll('.tab-panel').forEach(panel => {
        panel.classList.add('hidden');
    });

    // Remover clase active de todos los botones
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active', 'border-brand-600', 'text-brand-600');
        button.classList.add('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
    });

    // Mostrar panel seleccionado
    document.getElementById('panel-' + tab).classList.remove('hidden');

    // Activar botón seleccionado
    const activeButton = document.getElementById('tab-' + tab);
    activeButton.classList.add('active', 'border-brand-600', 'text-brand-600');
    activeButton.classList.remove('border-transparent', 'text-gray-500', 'hover:text-gray-700', 'hover:border-gray-300');
}

// Inicializar primer tab como activo
document.addEventListener('DOMContentLoaded', function() {
    cambiarTab('referidos');
});
</script>

<style>
.tab-button {
    transition: all 0.2s;
}
</style>
@endsection
