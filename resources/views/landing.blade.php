@extends('layouts.app')

@section('title', 'PickleTorneos')

@section('content')
    <!-- Header Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50">
        <div class="bg-white/95 backdrop-blur-md border-b border-gray-100/80 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <img src="{{ asset('images/logo-color.png') }}" alt="PickleTorneos" class="h-8 sm:h-9">
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden sm:flex items-center gap-2">
                        <a href="{{ route('tutoriales') }}"
                            class="text-gray-600 hover:text-brand-600 font-medium text-sm px-3 py-2 rounded-lg hover:bg-brand-50 transition duration-150">
                            Tutoriales
                        </a>
                        <a href="{{ route('login') }}"
                            class="text-gray-600 hover:text-brand-600 font-medium text-sm px-3 py-2 rounded-lg hover:bg-brand-50 transition duration-150">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-brand-600 text-white px-5 py-2 rounded-lg font-semibold text-sm hover:bg-brand-700 transition duration-150 shadow-md shadow-brand-100 ml-1">
                            Registrarse gratis
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="sm:hidden">
                        <button id="mobile-menu-button" type="button"
                            class="text-gray-600 hover:text-brand-600 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition">
                            <svg id="hamburger-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                            <svg id="close-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Mobile Menu -->
                <div id="mobile-menu" class="hidden sm:hidden pb-4 border-t border-gray-100 pt-3">
                    <div class="flex flex-col gap-1">
                        <a href="{{ route('tutoriales') }}"
                            class="text-gray-600 font-medium text-sm py-2.5 px-3 rounded-lg hover:bg-gray-50 transition">
                            Tutoriales
                        </a>
                        <a href="{{ route('login') }}"
                            class="text-gray-600 font-medium text-sm py-2.5 px-3 rounded-lg hover:bg-gray-50 transition">
                            Iniciar Sesión
                        </a>
                        <a href="{{ route('register') }}"
                            class="bg-brand-600 text-white px-4 py-3 rounded-xl font-semibold text-sm hover:bg-brand-700 transition text-center mt-2 shadow-md shadow-brand-100">
                            Registrarse gratis
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <script>
        document.getElementById('mobile-menu-button').addEventListener('click', function () {
            const menu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger-icon');
            const close = document.getElementById('close-icon');
            menu.classList.toggle('hidden');
            hamburger.classList.toggle('hidden');
            close.classList.toggle('hidden');
        });
    </script>

    <!-- Hero Section -->
    <section class="relative text-white overflow-hidden pt-16"
        style="background-image: url('{{ asset('images/banner.png') }}'); background-size: cover; background-position: center top;">
        <!-- Indigo overlay: image shows through subtly, blue tone dominates -->
        <div class="absolute inset-0" style="background-color: rgba(30, 27, 75, 0.82);"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 sm:py-20 lg:py-28">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <!-- Live pill -->
                    <div class="inline-flex items-center gap-2.5 bg-white/10 backdrop-blur-sm border border-white/15 px-4 py-2 rounded-full text-sm font-medium text-white/90 mb-7">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse flex-shrink-0"></span>
                        Sistema Profesional de Gestión de Torneos
                    </div>

                    <h1 class="text-4xl sm:text-5xl lg:text-6xl xl:text-[4.25rem] font-black leading-[1.06] tracking-tight mb-6">
                        Gestiona tus torneos<br class="hidden sm:block">
                        de forma
                        <span class="relative inline-block">
                            <span class="text-yellow-300">simple</span>
                        </span>
                        <br class="hidden lg:block">y profesional
                    </h1>

                    <p class="text-base sm:text-lg text-brand-100/80 mb-9 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                        Olvídate de las planillas de Excel y los grupos de WhatsApp. Organiza torneos de pádel, fútbol y más deportes con una plataforma todo-en-uno.
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mb-9">
                        <a href="{{ route('register') }}"
                            class="inline-flex items-center justify-center gap-2 bg-white text-brand-700 px-7 py-4 rounded-xl font-bold text-[0.95rem] hover:bg-accent-50 hover:text-brand-800 transition-all duration-200 shadow-2xl shadow-brand-950/60 hover:scale-[1.02]">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                            </svg>
                            Crear Primer Torneo Gratis
                        </a>
                        <a href="#como-funciona"
                            class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white px-7 py-4 rounded-xl font-semibold text-[0.95rem] hover:bg-white/20 transition-all duration-200">
                            Ver cómo funciona
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </a>
                    </div>

                    <!-- Trust row -->
                    <div class="flex flex-wrap items-center justify-center lg:justify-start gap-5 text-[0.8rem] text-white/60">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Primer torneo gratis
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Sin pagos mensuales
                        </span>
                        <span class="flex items-center gap-1.5">
                            <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                            </svg>
                            Soporte local
                        </span>
                    </div>
                </div>

                <!-- Right Visual -->
                <div class="hidden lg:flex justify-center">
                    <div class="relative" style="width: 360px;">
                        <!-- Screenshot -->
                        <img
                            src="{{ asset('images/screenshot1.jpg') }}"
                            alt="Vista de Llaves del Torneo"
                            class="relative rounded-3xl shadow-2xl border border-white/15 object-cover w-full hover:scale-[1.02] transition-transform duration-500"
                            style="max-height: 520px;"
                        >

                        <!-- Floating: Live badge -->
                        <div class="absolute -left-10 top-10 bg-white rounded-2xl shadow-xl px-4 py-3 border border-gray-100/80">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <span class="w-2.5 h-2.5 bg-green-500 rounded-full animate-pulse block"></span>
                                </div>
                                <div>
                                    <p class="text-[0.7rem] text-gray-400 font-medium leading-none mb-0.5">Estado</p>
                                    <p class="text-sm font-bold text-gray-800">Torneo en vivo</p>
                                </div>
                            </div>
                        </div>

                        <!-- Floating: Players badge -->
                        <div class="absolute -right-8 bottom-14 bg-white rounded-2xl shadow-xl px-4 py-3 border border-gray-100/80">
                            <div class="flex items-center gap-2.5">
                                <div class="w-9 h-9 bg-brand-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                    <svg class="w-4.5 h-4.5 text-brand-600" fill="currentColor" viewBox="0 0 20 20" style="width:18px;height:18px;">
                                        <path d="M9 6a3 3 0 11-6 0 3 3 0 016 0zM17 6a3 3 0 11-6 0 3 3 0 016 0zM12.93 17c.046-.327.07-.66.07-1a6.97 6.97 0 00-1.5-4.33A5 5 0 0119 16v1h-6.07zM6 11a5 5 0 015 5v1H1v-1a5 5 0 015-5z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-[0.7rem] text-gray-400 font-medium leading-none mb-0.5">Participantes</p>
                                    <p class="text-sm font-bold text-gray-800">32 equipos</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wave Separator -->
        <div class="relative h-12 sm:h-16 lg:h-20 -mb-1">
            <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg"
                class="absolute bottom-0 w-full" preserveAspectRatio="none" style="height:100%;">
                <path d="M0,40L80,44C160,48,320,56,480,52C640,48,800,30,960,26C1120,22,1280,32,1360,36L1440,40L1440,80L0,80Z"
                    fill="#F9FAFB" />
            </svg>
        </div>
    </section>

    <!-- Pricing Section -->
    <section class="bg-gray-50 py-16 sm:py-20 lg:py-24">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="inline-block bg-brand-50 text-brand-600 border border-brand-100 px-4 py-1.5 rounded-full text-xs font-bold tracking-wider uppercase mb-4">Precios</span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mb-3 tracking-tight">
                    Precio simple y transparente
                </h2>
                <p class="text-base sm:text-lg text-gray-500 max-w-md mx-auto">
                    Sin sorpresas. Pagas solo cuando organizas un torneo.
                </p>
            </div>

            <div class="max-w-3xl mx-auto grid md:grid-cols-2 gap-6 md:gap-4 items-stretch">
                <!-- Free Plan -->
                <div class="bg-white rounded-3xl p-7 sm:p-8 border border-gray-200 hover:border-brand-200 hover:shadow-lg transition-all duration-300 flex flex-col">
                    <div class="mb-7">
                        <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-100 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-5">
                            <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                            Gratis para probar
                        </span>
                        <h3 class="text-xl font-bold text-gray-900 mb-3">Primer Torneo</h3>
                        <div class="flex items-baseline gap-1">
                            <span class="text-5xl font-black text-gray-900">$0</span>
                        </div>
                        <p class="text-gray-400 text-sm mt-1.5">Sin límites de funcionalidades</p>
                    </div>

                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="w-5 h-5 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 border border-green-100">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Participantes ilimitados
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="w-5 h-5 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 border border-green-100">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Todas las funcionalidades incluidas
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="w-5 h-5 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 border border-green-100">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Notificaciones (sistema, email y SMS)
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="w-5 h-5 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 border border-green-100">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Gestión de llaves y resultados
                        </li>
                        <li class="flex items-center gap-3 text-sm text-gray-600">
                            <span class="w-5 h-5 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 border border-green-100">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Acceso web para jugadores
                        </li>
                    </ul>

                    <a href="{{ route('register') }}"
                        class="block w-full bg-gray-900 text-white text-center px-6 py-3.5 rounded-xl font-semibold text-sm hover:bg-gray-700 transition duration-200">
                        Comenzar Gratis
                    </a>
                </div>

                <!-- Paid Plan -->
                @php
                    $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);
                @endphp
                <div class="relative bg-brand-700 rounded-3xl p-7 sm:p-8 border border-brand-600/30 shadow-2xl shadow-brand-200/50 md:scale-[1.03] flex flex-col">
                    <!-- Top badge breaking out -->
                    <div class="absolute -top-4 inset-x-0 flex justify-center pointer-events-none">
                        <span class="bg-accent-400 text-gray-900 px-5 py-1.5 rounded-full text-[0.7rem] font-black tracking-widest uppercase shadow-lg">
                            Más popular
                        </span>
                    </div>

                    <div class="mb-7 mt-2">
                        <h3 class="text-xl font-bold text-white mb-3">Por Torneo</h3>
                        <div class="flex items-baseline gap-1">
                            <span class="text-5xl font-black text-white">${{ number_format($precioTorneo, 0, ',', '.') }}</span>
                        </div>
                        <p class="text-brand-200/80 text-sm mt-1.5">por cada torneo adicional</p>
                    </div>

                    <ul class="space-y-3 mb-8 flex-1">
                        <li class="flex items-center gap-3 text-sm text-white/90 font-semibold">
                            <span class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-2.5 h-2.5 text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Todo lo del plan gratis
                        </li>
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-2.5 h-2.5 text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Pago único por torneo
                        </li>
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-2.5 h-2.5 text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Sin suscripciones mensuales
                        </li>
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-2.5 h-2.5 text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Soporte prioritario
                        </li>
                        <li class="flex items-center gap-3 text-sm text-white/80">
                            <span class="w-5 h-5 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="w-2.5 h-2.5 text-accent-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            Historial ilimitado de torneos
                        </li>
                    </ul>

                    <a href="{{ route('register') }}"
                        class="block w-full bg-white text-brand-700 text-center px-6 py-3.5 rounded-xl font-bold text-sm hover:bg-accent-50 transition duration-200 shadow-lg shadow-brand-900/40">
                        Comenzar Ahora
                    </a>
                </div>
            </div>

            <p class="mt-10 text-center text-gray-400 text-xs flex items-center justify-center gap-2">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
                Pago seguro con Mercado Pago &nbsp;·&nbsp; Sin comisiones ocultas &nbsp;·&nbsp; Cancela cuando quieras
            </p>
        </div>
    </section>

    <!-- Referral Program Section -->
    <section class="bg-white py-16 sm:py-20 lg:py-24 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="inline-flex items-center gap-2 bg-green-50 text-green-700 border border-green-100 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4">
                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M5 3a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2V5a2 2 0 00-2-2H5zM5 11a2 2 0 00-2 2v2a2 2 0 002 2h2a2 2 0 002-2v-2a2 2 0 00-2-2H5zM11 5a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V5zM14 11a1 1 0 011 1v1h1a1 1 0 110 2h-1v1a1 1 0 11-2 0v-1h-1a1 1 0 110-2h1v-1a1 1 0 011-1z"/></svg>
                    Programa de referidos
                </span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mb-3 tracking-tight">
                    Gana torneos gratis invitando colegas
                </h2>
                <p class="text-base sm:text-lg text-gray-500 max-w-xl mx-auto">
                    Comparte PickleTorneos con otros organizadores y obtén beneficios increíbles
                </p>
            </div>

            <div class="grid md:grid-cols-2 gap-6 mb-10 max-w-4xl mx-auto">
                <!-- Para el Referido -->
                <div class="bg-gray-50 rounded-3xl p-7 sm:p-8 border border-gray-200 hover:border-blue-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Para vos (Nuevo Usuario)</h3>
                            <p class="text-blue-600 text-sm font-medium">Al registrarte con un código</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-200">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Primer torneo GRATIS</p>
                                <p class="text-gray-500 text-xs mt-0.5">Sin pagar nada, crea tu primer torneo completo</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-200">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                @php
                                    $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000);
                                    $porcentajeDescuento = \App\Models\ConfiguracionSistema::get('porcentaje_descuento_referido', 20);
                                    $montoDescuento = $precioTorneo * ($porcentajeDescuento / 100);
                                @endphp
                                <p class="font-semibold text-gray-900 text-sm">{{ $porcentajeDescuento }}% de descuento en tu segundo torneo</p>
                                <p class="text-gray-500 text-xs mt-0.5">Ahorra ${{ number_format($montoDescuento, 0, ',', '.') }} en tu primer pago</p>
                            </div>
                        </div>

                        <div class="bg-blue-50 rounded-2xl p-4 mt-5 border border-blue-100">
                            <p class="text-blue-900 font-bold text-sm text-center">
                                Total ahorrado: ${{ number_format($precioTorneo + $montoDescuento, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Para el Referidor -->
                <div class="bg-gray-50 rounded-3xl p-7 sm:p-8 border border-gray-200 hover:border-green-200 hover:shadow-lg transition-all duration-300">
                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                            <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Para vos (Referidor)</h3>
                            <p class="text-green-600 text-sm font-medium">Por cada colega que invites</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-200">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">1 Torneo Gratis por cada referido</p>
                                <p class="text-gray-500 text-xs mt-0.5">Cuando tu referido pague su primer torneo</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-200">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Sin límite de referidos</p>
                                <p class="text-gray-500 text-xs mt-0.5">Invita a todos los que quieras y acumula créditos</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-200">
                                <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            </span>
                            <div>
                                <p class="font-semibold text-gray-900 text-sm">Créditos válidos por 12 meses</p>
                                <p class="text-gray-500 text-xs mt-0.5">Úsalos cuando quieras, sin apuro</p>
                            </div>
                        </div>

                        <div class="bg-green-50 rounded-2xl p-4 mt-5 border border-green-100">
                            <p class="text-green-900 font-bold text-sm text-center">
                                Valor por referido: ${{ number_format($precioTorneo, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CTA referidos -->
            <div class="bg-brand-800 rounded-3xl p-8 sm:p-12 text-center shadow-2xl shadow-brand-100 max-w-4xl mx-auto">
                <h3 class="text-2xl sm:text-3xl font-black text-white mb-3 tracking-tight">
                    ¿Listo para ganar torneos gratis?
                </h3>
                <p class="text-brand-100/80 text-base mb-8 max-w-xl mx-auto">
                    Regístrate ahora y obtén tu código de referido único. Compártelo con tus colegas y empieza a acumular beneficios.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('register') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white text-brand-700 font-bold rounded-xl hover:bg-accent-50 transition duration-200 shadow-lg text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        Crear mi Cuenta Gratis
                    </a>
                    @auth
                    <a href="{{ route('referidos.dashboard') }}"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white/15 backdrop-blur-sm border border-white/25 text-white font-semibold rounded-xl hover:bg-white/25 transition duration-200 text-sm">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                        </svg>
                        Ver mi Dashboard de Referidos
                    </a>
                    @endauth
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="como-funciona" class="bg-gray-50 py-16 sm:py-20 lg:py-24 border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <span class="inline-block bg-purple-50 text-purple-700 border border-purple-100 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider mb-4">Funcionalidades</span>
                <h2 class="text-3xl sm:text-4xl font-black text-gray-900 mb-3 tracking-tight">
                    Todo lo que necesitas para gestionar torneos profesionales
                </h2>
                <p class="text-base sm:text-lg text-gray-500 max-w-xl mx-auto">
                    Ahorra tiempo, evita errores y ofrece una experiencia profesional a tus jugadores
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
                <!-- Feature 1 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-brand-100 hover:shadow-md transition-all duration-200 group">
                    <div class="w-11 h-11 bg-brand-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-brand-100 transition-colors duration-200">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Gestión de Jugadores</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Carga jugadores y parejas fácilmente. Organiza por categorías y lleva un ranking actualizado.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-purple-100 hover:shadow-md transition-all duration-200 group">
                    <div class="w-11 h-11 bg-purple-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-purple-100 transition-colors duration-200">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Generación de Llaves</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Crea automáticamente llaves de eliminación directa, fase de grupos o sistemas mixtos.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-green-100 hover:shadow-md transition-all duration-200 group">
                    <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-green-100 transition-colors duration-200">
                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Horarios y Canchas</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Asigna horarios y canchas a cada partido. Visualiza el cronograma completo del torneo.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-accent-100 hover:shadow-md transition-all duration-200 group">
                    <div class="w-11 h-11 bg-accent-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-accent-100 transition-colors duration-200">
                        <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Resultados en Vivo</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Carga resultados y avanza automáticamente en las llaves. Todo actualizado en tiempo real.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-red-100 hover:shadow-md transition-all duration-200 group">
                    <div class="w-11 h-11 bg-red-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-red-100 transition-colors duration-200">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Notificaciones Automáticas</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Los jugadores reciben alertas por sistema, email y SMS de sus partidos, cambios y resultados.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-blue-100 hover:shadow-md transition-all duration-200 group">
                    <div class="w-11 h-11 bg-blue-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-blue-100 transition-colors duration-200">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Acceso para Jugadores</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Los jugadores acceden desde cualquier dispositivo para ver horarios, llaves y resultados en vivo.
                    </p>
                </div>

                <!-- Feature 7 -->
                <div class="bg-white rounded-2xl p-6 border border-gray-100 hover:border-teal-100 hover:shadow-md transition-all duration-200 group md:col-span-2 lg:col-span-1">
                    <div class="w-11 h-11 bg-teal-50 rounded-xl flex items-center justify-center mb-4 group-hover:bg-teal-100 transition-colors duration-200">
                        <svg class="w-5 h-5 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 mb-2">Historial y Estadísticas</h3>
                    <p class="text-gray-500 text-sm leading-relaxed">
                        Consultá el historial completo de torneos anteriores y estadísticas. Toda la info para el próximo evento.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="relative text-white py-16 sm:py-20 lg:py-28 overflow-hidden"
        style="background-image: url('{{ asset('images/banner2.png') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0" style="background-color: rgba(30, 27, 75, 0.82);"></div>
        <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black mb-5 tracking-tight leading-tight">
                ¿Listo para profesionalizar tus torneos?
            </h2>
            <p class="text-lg text-brand-100/80 mb-10 max-w-xl mx-auto">
                Únete a los organizadores que ya confían en PickleTorneos para gestionar sus eventos deportivos.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}"
                    class="inline-flex items-center justify-center gap-2 bg-white text-brand-700 px-8 py-4 rounded-xl font-bold text-base hover:bg-accent-50 transition-all duration-200 shadow-2xl shadow-brand-950/60 hover:scale-[1.02]">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Crear Torneo Gratis
                </a>
                <a href="{{ route('login') }}"
                    class="inline-flex items-center justify-center bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-4 rounded-xl font-semibold text-base hover:bg-white/20 transition-all duration-200">
                    Ya tengo cuenta
                </a>
            </div>
            <p class="mt-8 text-xs text-brand-200/60">
                Primer torneo completamente gratis &nbsp;·&nbsp; Sin pagos mensuales &nbsp;·&nbsp; Soporte incluido
            </p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center gap-6">
                <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-9 opacity-80">
                <p class="text-gray-500 text-sm">Sistema profesional de gestión de torneos deportivos</p>

                <!-- Redes sociales -->
                <div class="flex items-center gap-4">
                    <!-- Email -->
                    <a href="mailto:pickletorneossde@gmail.com"
                        class="w-9 h-9 bg-gray-800 hover:bg-brand-600 rounded-lg flex items-center justify-center transition-colors duration-200 group" title="pickletorneossde@gmail.com">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </a>
                    <!-- WhatsApp -->
                    <a href="https://wa.me/543857477092" target="_blank"
                        class="w-9 h-9 bg-gray-800 hover:bg-green-600 rounded-lg flex items-center justify-center transition-colors duration-200 group" title="+54 385 747 7092">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                        </svg>
                    </a>
                    <!-- Instagram -->
                    <a href="https://instagram.com/pickletorneossde" target="_blank"
                        class="w-9 h-9 bg-gray-800 hover:bg-pink-600 rounded-lg flex items-center justify-center transition-colors duration-200 group" title="@pickletorneossde">
                        <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                        </svg>
                    </a>
                </div>

                <div class="flex items-center gap-6 text-xs text-gray-600">
                    <a href="{{ route('tyc') }}" class="hover:text-gray-400 transition duration-200">Términos y Condiciones</a>
                </div>
                <p class="text-xs text-gray-700 pt-2 border-t border-gray-800/60 w-full text-center">
                    © {{date('Y')}} PickleTorneos. Todos los derechos reservados.
                </p>
            </div>
        </div>
    </footer>

    <!-- Floating WhatsApp Button -->
    <a href="https://wa.me/543857477092" target="_blank"
        class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/40 hover:scale-110 transition-all duration-200"
        title="Contactar por WhatsApp">
        <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
        </svg>
    </a>
@endsection
