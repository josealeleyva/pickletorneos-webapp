@extends('layouts.app')

@section('title', 'Invitación a Punto de Oro')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-indigo-50 via-white to-purple-50">
    <div class="container mx-auto px-4 py-12">
        <!-- Header -->
        <div class="text-center mb-12">
            <img src="{{ asset('images/logo.png') }}" alt="Punto de Oro" class="h-16 mx-auto mb-6">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
                ¡Has sido invitado! 🎉
            </h1>
            <p class="text-xl text-gray-600">
                <strong>{{ $referidor->name }} {{ $referidor->apellido }}</strong> te invita a unirte a Punto de Oro
            </p>
        </div>

        <!-- Card Principal -->
        <div class="max-w-4xl mx-auto">
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
                <!-- Banner del Referidor -->
                <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white">
                    <div class="flex items-center gap-6">
                        <div class="bg-white/20 backdrop-blur-sm rounded-full p-6">
                            <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-indigo-100 text-sm mb-1">Te invita:</p>
                            <h2 class="text-3xl font-bold">{{ $referidor->name }} {{ $referidor->apellido }}</h2>
                            @if($referidor->organizacion)
                                <p class="text-indigo-100 mt-2">{{ $referidor->organizacion }}</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contenido -->
                <div class="p-8">
                    <!-- Beneficio Destacado -->
                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-6 mb-8">
                        <div class="flex items-start gap-4">
                            <div class="bg-green-500 rounded-full p-3 flex-shrink-0">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            @php
                                $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);
                                $porcentajeDescuento = \App\Models\ConfiguracionSistema::get('porcentaje_descuento_referido', 20);
                                $montoDescuento = $precioTorneo * ($porcentajeDescuento / 100);
                            @endphp
                            <div>
                                <h3 class="text-2xl font-bold text-green-900 mb-2">¡{{ $porcentajeDescuento }}% de DESCUENTO!</h3>
                                <p class="text-green-700 text-lg">
                                    Obtén <strong>${{ number_format($montoDescuento, 0, ',', '.') }} de descuento</strong> en tu primer torneo pago usando el código de referido de {{ $referidor->name }}.
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Qué es Punto de Oro -->
                    <div class="mb-8">
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">¿Qué es Punto de Oro?</h3>
                        <p class="text-gray-600 text-lg mb-4">
                            Es la plataforma líder para gestionar torneos deportivos de Padel, Fútbol y Tenis. Simplifica la organización de tus eventos deportivos con herramientas profesionales.
                        </p>
                    </div>

                    <!-- Características -->
                    <div class="grid md:grid-cols-2 gap-6 mb-8">
                        <div class="flex items-start gap-3">
                            <div class="bg-indigo-100 rounded-lg p-2 flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Gestión Completa</h4>
                                <p class="text-gray-600 text-sm">Crea y administra torneos con diferentes formatos: eliminación directa, grupos, liga.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-indigo-100 rounded-lg p-2 flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Programación Automática</h4>
                                <p class="text-gray-600 text-sm">Genera fixtures y horarios automáticamente para tus partidos.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-indigo-100 rounded-lg p-2 flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Notificaciones</h4>
                                <p class="text-gray-600 text-sm">Envía recordatorios automáticos a los jugadores sobre sus partidos.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="bg-indigo-100 rounded-lg p-2 flex-shrink-0">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-900 mb-1">Tablas y Estadísticas</h4>
                                <p class="text-gray-600 text-sm">Visualiza posiciones, resultados y estadísticas en tiempo real.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Precio -->
                    <div class="bg-gray-50 rounded-xl p-6 mb-8">
                        <div class="text-center">
                            @php
                                $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);
                                $porcentajeDescuento = \App\Models\ConfiguracionSistema::get('porcentaje_descuento_referido', 20);
                                $montoDescuento = $precioTorneo * ($porcentajeDescuento / 100);
                                $precioConDescuento = $precioTorneo - $montoDescuento;
                            @endphp
                            <p class="text-gray-600 mb-2">Precio por torneo:</p>
                            <div class="flex items-center justify-center gap-4">
                                <span class="text-3xl font-bold text-gray-400 line-through">${{ number_format($precioTorneo, 0, ',', '.') }}</span>
                                <span class="text-5xl font-bold text-indigo-600">${{ number_format($precioConDescuento, 0, ',', '.') }}</span>
                            </div>
                            <p class="text-green-600 font-semibold mt-2">¡Ahorras ${{ number_format($montoDescuento, 0, ',', '.') }} con el código de {{ $referidor->name }}!</p>
                            <p class="text-gray-500 text-sm mt-3">* Tu primer torneo es GRATIS. Este precio aplica a partir del segundo torneo.</p>
                        </div>
                    </div>

                    <!-- CTA -->
                    <div class="text-center">
                        <a href="{{ route('register', ['ref' => $codigo]) }}" class="inline-block bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-8 py-4 rounded-xl font-bold text-lg hover:from-indigo-700 hover:to-purple-700 transition transform hover:scale-105 shadow-xl">
                            Crear mi cuenta GRATIS
                        </a>
                        <p class="text-gray-500 text-sm mt-4">
                            El código <strong>{{ $codigo }}</strong> se aplicará automáticamente
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-200">
                    <div class="flex items-center justify-center gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        <span>Sin compromiso · Primer torneo GRATIS · Cancela cuando quieras</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Testimonios o info adicional -->
        <div class="max-w-4xl mx-auto mt-12 text-center">
            <p class="text-gray-600">
                ¿Tienes preguntas? Contáctanos en <a href="mailto:info@puntodeoro.com" class="text-indigo-600 hover:underline">info@puntodeoro.com</a>
            </p>
        </div>
    </div>
</div>
@endsection
