@extends('layouts.app')

@section('title', 'PickleTorneos — Gestión profesional de torneos de Pickleball')

@push('styles')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Plus Jakarta Sans', sans-serif; }
    .font-display { font-family: 'Barlow Condensed', sans-serif; }

    /* Court pattern texture */
    .court-texture {
        background-image:
            linear-gradient(rgba(15,107,120,0.06) 1px, transparent 1px),
            linear-gradient(90deg, rgba(15,107,120,0.06) 1px, transparent 1px);
        background-size: 40px 40px;
    }

    /* Diagonal slash divider */
    .slash-divider {
        clip-path: polygon(0 0, 100% 0, 100% 80%, 0 100%);
    }

    /* Animated underline for hero */
    .orange-underline {
        background: linear-gradient(90deg, #FF6A00, #ff9240);
        background-repeat: no-repeat;
        background-size: 100% 4px;
        background-position: 0 100%;
        padding-bottom: 4px;
    }

    /* Feature card left accent */
    .feature-card::before {
        content: '';
        position: absolute;
        left: 0;
        top: 16px;
        bottom: 16px;
        width: 3px;
        background: linear-gradient(180deg, #FF6A00, #0F6B78);
        border-radius: 0 2px 2px 0;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .feature-card:hover::before { opacity: 1; }

    /* Stat number shine */
    .stat-number {
        background: linear-gradient(135deg, #FF6A00 0%, #ffb47a 50%, #FF6A00 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* Hero badge pulse ring */
    @keyframes ping-slow {
        0% { transform: scale(1); opacity: 0.6; }
        100% { transform: scale(2.2); opacity: 0; }
    }
    .ping-slow { animation: ping-slow 2.2s cubic-bezier(0,0,0.2,1) infinite; }

    /* Scroll reveal */
    .reveal { opacity: 0; transform: translateY(24px); transition: opacity 0.6s ease, transform 0.6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
</style>
@endpush

@section('content')

{{-- ═══════════════════════════════════════════════
     NAVBAR
═══════════════════════════════════════════════ --}}
<nav class="fixed top-0 left-0 right-0 z-50 bg-white/95 backdrop-blur-md border-b border-gray-100/80 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <img src="{{ asset('images/logo-color.png') }}" alt="PickleTorneos" class="h-8 sm:h-9">
            </div>

            {{-- Desktop nav --}}
            <div class="hidden sm:flex items-center gap-2">
                <a href="{{ route('tutoriales') }}" class="text-gray-600 hover:text-brand-700 font-medium text-sm px-3 py-2 rounded-lg hover:bg-brand-50 transition duration-150">Tutoriales</a>
                <a href="{{ route('login') }}" class="text-gray-600 hover:text-brand-700 font-medium text-sm px-3 py-2 rounded-lg hover:bg-brand-50 transition duration-150">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="bg-accent-600 text-white px-5 py-2 rounded-lg font-semibold text-sm hover:bg-accent-700 transition duration-150 shadow-md shadow-accent-200 ml-1">
                    Registrarse gratis
                </a>
            </div>

            {{-- Mobile toggle --}}
            <div class="sm:hidden">
                <button id="mobile-menu-button" type="button" class="text-gray-600 hover:text-brand-700 focus:outline-none p-2 rounded-lg hover:bg-gray-100 transition">
                    <svg id="hamburger-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                    <svg id="close-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Mobile menu --}}
        <div id="mobile-menu" class="hidden sm:hidden pb-4 border-t border-gray-100 pt-3">
            <div class="flex flex-col gap-1">
                <a href="{{ route('tutoriales') }}" class="text-gray-600 font-medium text-sm py-2.5 px-3 rounded-lg hover:bg-gray-50 transition">Tutoriales</a>
                <a href="{{ route('login') }}" class="text-gray-600 font-medium text-sm py-2.5 px-3 rounded-lg hover:bg-gray-50 transition">Iniciar Sesión</a>
                <a href="{{ route('register') }}" class="bg-accent-600 text-white px-4 py-3 rounded-xl font-semibold text-sm hover:bg-accent-700 transition text-center mt-2 shadow-md">Registrarse gratis</a>
            </div>
        </div>
    </div>
</nav>

<script>
    document.getElementById('mobile-menu-button').addEventListener('click', function () {
        document.getElementById('mobile-menu').classList.toggle('hidden');
        document.getElementById('hamburger-icon').classList.toggle('hidden');
        document.getElementById('close-icon').classList.toggle('hidden');
    });
</script>


{{-- ═══════════════════════════════════════════════
     HERO
═══════════════════════════════════════════════ --}}
<section class="relative text-white overflow-hidden pt-16"
    style="background-image: url('{{ asset('images/banner.png') }}'); background-size: cover; background-position: center top; min-height: 620px;">

    {{-- Teal dark overlay (NOT indigo) --}}
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(10,56,66,0.93) 0%, rgba(13,72,85,0.88) 50%, rgba(10,56,66,0.75) 100%);"></div>

    {{-- Decorative diagonal court lines --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute" style="top:-10%; right:-5%; width:55%; height:120%; border-left: 1.5px solid rgba(255,106,0,0.12); border-right: 1.5px solid rgba(255,255,255,0.05); transform: skewX(-8deg);"></div>
        <div class="absolute" style="top:-10%; right:15%; width:2px; height:120%; background: rgba(255,106,0,0.08); transform: skewX(-8deg);"></div>
    </div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 sm:py-22 lg:py-28">
        <div class="grid lg:grid-cols-2 gap-12 lg:gap-16 items-center">

            {{-- Left content --}}
            <div class="text-center lg:text-left">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2.5 bg-white/10 backdrop-blur-sm border border-white/15 px-4 py-2 rounded-full text-sm font-medium text-white/90 mb-7">
                    <span class="relative flex h-2 w-2">
                        <span class="ping-slow absolute inline-flex h-full w-full rounded-full bg-accent-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-accent-400"></span>
                    </span>
                    🏓 Plataforma #1 para Torneos de Pickleball
                </div>

                <h1 class="font-display text-5xl sm:text-6xl lg:text-7xl xl:text-[5rem] font-black leading-[0.95] tracking-tight mb-6 uppercase">
                    Organizá torneos de<br>
                    <span class="orange-underline text-accent-400">Pickleball</span><br>
                    como un pro
                </h1>

                <p class="text-base sm:text-lg text-white/70 mb-9 max-w-lg mx-auto lg:mx-0 leading-relaxed">
                    Inscripciones, llaves, fixture, canchas y resultados en tiempo real — todo desde una sola plataforma diseñada para el pickleball argentino.
                </p>

                {{-- CTAs --}}
                <div class="flex flex-col sm:flex-row gap-3 justify-center lg:justify-start mb-10">
                    <a href="{{ route('register') }}"
                        class="inline-flex items-center justify-center gap-2 bg-accent-600 text-white px-7 py-4 rounded-xl font-bold text-[0.95rem] hover:bg-accent-500 transition-all duration-200 shadow-2xl shadow-accent-900/50 hover:scale-[1.02]">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        Crear Mi Primer Torneo Gratis
                    </a>
                    <a href="#como-funciona"
                        class="inline-flex items-center justify-center gap-2 bg-white/10 backdrop-blur-sm border border-white/20 text-white px-7 py-4 rounded-xl font-semibold text-[0.95rem] hover:bg-white/20 transition-all duration-200">
                        Ver cómo funciona
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </a>
                </div>

                {{-- Trust row --}}
                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-5 text-[0.78rem] text-white/55">
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Primer torneo 100% gratis
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Inscripción online de parejas
                    </span>
                    <span class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 text-green-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        Soporte local Argentina
                    </span>
                </div>
            </div>

            {{-- Right visual --}}
            <div class="hidden lg:flex justify-center">
                <div class="relative" style="width: 360px;">
                    <img
                        src="{{ asset('images/screenshot1.jpg') }}"
                        alt="Llaves de Pickleball"
                        class="relative rounded-3xl shadow-2xl border border-white/15 object-cover w-full hover:scale-[1.02] transition-transform duration-500"
                        style="max-height: 520px;"
                    >

                    {{-- Floating badge: live --}}
                    <div class="absolute -left-10 top-10 bg-white rounded-2xl shadow-xl px-4 py-3 border border-gray-100/80">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-green-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="relative flex h-2.5 w-2.5">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-green-500"></span>
                                </span>
                            </div>
                            <div>
                                <p class="text-[0.65rem] text-gray-400 font-medium leading-none mb-0.5">Estado</p>
                                <p class="text-sm font-bold text-gray-800">Torneo en vivo</p>
                            </div>
                        </div>
                    </div>

                    {{-- Floating badge: parejas --}}
                    <div class="absolute -right-8 bottom-14 bg-white rounded-2xl shadow-xl px-4 py-3 border border-gray-100/80">
                        <div class="flex items-center gap-2.5">
                            <div class="w-9 h-9 bg-accent-50 rounded-xl flex items-center justify-center flex-shrink-0">
                                {{-- Paddle icon --}}
                                <svg class="w-5 h-5 text-accent-600" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M7.5 3C4.46 3 2 5.46 2 8.5c0 2.19 1.28 4.08 3.14 5.01L2.29 16.36a1 1 0 000 1.41l4.24 4.24a1 1 0 001.41 0l2.85-2.85C11.72 20.72 12.86 21 14 21c3.31 0 6-2.69 6-6 0-1.14-.28-2.28-.79-3.21l.5-.5a1 1 0 000-1.41l-1.5-1.5-1.41 1.41.79.79-5.65 5.66-.79-.79-1.41 1.41 1.5 1.5a1 1 0 001.41 0l.5-.5C13.28 18.72 13.64 19 14 19c2.21 0 4-1.79 4-4s-1.79-4-4-4c-.36 0-.72.04-1.06.12l-.44-.44 1.41-1.41-.79-.79 1.5-1.5a1 1 0 000-1.41l-1.5-1.5a1 1 0 00-1.41 0l-.5.5C10.28 4.28 9.14 4 8 4"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-[0.65rem] text-gray-400 font-medium leading-none mb-0.5">Categorías</p>
                                <p class="text-sm font-bold text-gray-800">Masc · Fem · Mixto</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave --}}
    <div class="relative h-12 sm:h-16 lg:h-20 -mb-1">
        <svg viewBox="0 0 1440 80" fill="none" xmlns="http://www.w3.org/2000/svg"
            class="absolute bottom-0 w-full" preserveAspectRatio="none" style="height:100%;">
            <path d="M0,40L80,44C160,48,320,56,480,52C640,48,800,30,960,26C1120,22,1280,32,1360,36L1440,40L1440,80L0,80Z"
                fill="#F9FAFB"/>
        </svg>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     STATS BAR
═══════════════════════════════════════════════ --}}
<section class="bg-gray-50 py-12 sm:py-14">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-3 gap-4 sm:gap-8 text-center">
            <div class="reveal">
                <p class="font-display stat-number text-4xl sm:text-5xl font-black">+50</p>
                <p class="text-gray-500 text-xs sm:text-sm font-medium mt-1">Torneos organizados</p>
            </div>
            <div class="reveal" style="transition-delay:0.1s">
                <p class="font-display stat-number text-4xl sm:text-5xl font-black">+800</p>
                <p class="text-gray-500 text-xs sm:text-sm font-medium mt-1">Jugadores registrados</p>
            </div>
            <div class="reveal" style="transition-delay:0.2s">
                <p class="font-display stat-number text-4xl sm:text-5xl font-black">100%</p>
                <p class="text-gray-500 text-xs sm:text-sm font-medium mt-1">Hecho para pickleball</p>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     CÓMO FUNCIONA (3 STEPS)
═══════════════════════════════════════════════ --}}
<section id="como-funciona" class="bg-white py-16 sm:py-20 lg:py-24 border-t border-gray-100 court-texture">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <span class="inline-flex items-center gap-2 border-l-4 border-accent-600 pl-3 text-xs font-bold uppercase tracking-widest text-accent-600 mb-4">
                Así funciona
            </span>
            <h2 class="font-display text-4xl sm:text-5xl font-black text-gray-900 tracking-tight uppercase">
                De cero a torneo<br class="hidden sm:block"> en minutos
            </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8 relative">
            {{-- Connector line (desktop) --}}
            <div class="hidden md:block absolute top-10 left-1/4 right-1/4 h-0.5 bg-gradient-to-r from-accent-200 via-brand-200 to-accent-200 mx-16"></div>

            {{-- Step 1 --}}
            <div class="relative text-center reveal">
                <div class="w-20 h-20 bg-accent-600 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-accent-200 rotate-3 hover:rotate-0 transition-transform duration-300">
                    <span class="font-display text-3xl font-black text-white">01</span>
                </div>
                <h3 class="font-display text-xl font-bold text-gray-900 uppercase tracking-wide mb-2">Creá el torneo</h3>
                <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">
                    Elegí el formato: eliminación directa, fase de grupos o liga. Configurá categorías (Masculino, Femenino, Mixto) y abrí las inscripciones.
                </p>
            </div>

            {{-- Step 2 --}}
            <div class="relative text-center reveal" style="transition-delay:0.15s">
                <div class="w-20 h-20 bg-brand-700 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-brand-200 -rotate-2 hover:rotate-0 transition-transform duration-300">
                    <span class="font-display text-3xl font-black text-white">02</span>
                </div>
                <h3 class="font-display text-xl font-bold text-gray-900 uppercase tracking-wide mb-2">Armá las llaves</h3>
                <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">
                    Con las parejas inscriptas, generá el fixture automáticamente. El sistema asigna canchas, horarios y arma el bracket en segundos.
                </p>
            </div>

            {{-- Step 3 --}}
            <div class="relative text-center reveal" style="transition-delay:0.3s">
                <div class="w-20 h-20 bg-gray-900 rounded-2xl flex items-center justify-center mx-auto mb-5 shadow-lg shadow-gray-200 rotate-2 hover:rotate-0 transition-transform duration-300">
                    <span class="font-display text-3xl font-black text-white">03</span>
                </div>
                <h3 class="font-display text-xl font-bold text-gray-900 uppercase tracking-wide mb-2">Jugá y seguí en vivo</h3>
                <p class="text-gray-500 text-sm leading-relaxed max-w-xs mx-auto">
                    Los jugadores ven sus partidos, canchas y resultados desde el celular. Cargá resultados y el sistema avanza las llaves automáticamente.
                </p>
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     FEATURES
═══════════════════════════════════════════════ --}}
<section class="bg-gray-50 py-16 sm:py-20 lg:py-24 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <span class="inline-flex items-center gap-2 border-l-4 border-brand-700 pl-3 text-xs font-bold uppercase tracking-widest text-brand-700 mb-4">
                Funcionalidades
            </span>
            <h2 class="font-display text-4xl sm:text-5xl font-black text-gray-900 tracking-tight uppercase">
                Todo lo que un torneo<br class="hidden sm:block"> de pickleball necesita
            </h2>
            <p class="text-base text-gray-500 max-w-xl mx-auto mt-3">
                Pensado para organizadores reales. Sin complicaciones, sin Excel, sin grupos de WhatsApp.
            </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-5">

            <div class="feature-card relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-brand-200 hover:shadow-lg transition-all duration-200 reveal">
                <div class="w-11 h-11 bg-brand-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <h3 class="font-display text-base font-bold text-gray-900 uppercase tracking-wide mb-2">Inscripción de Parejas</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Inscripciones online con invitación al compañero. El sistema confirma automáticamente cuando ambos aceptan.
                </p>
            </div>

            <div class="feature-card relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-accent-200 hover:shadow-lg transition-all duration-200 reveal" style="transition-delay:0.05s">
                <div class="w-11 h-11 bg-accent-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <h3 class="font-display text-base font-bold text-gray-900 uppercase tracking-wide mb-2">Llaves y Brackets</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Genera el bracket automáticamente. Eliminación directa, fase de grupos o sistema mixto según el torneo.
                </p>
            </div>

            <div class="feature-card relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-green-200 hover:shadow-lg transition-all duration-200 reveal" style="transition-delay:0.1s">
                <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="font-display text-base font-bold text-gray-900 uppercase tracking-wide mb-2">Canchas y Horarios</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Asigna cada partido a una cancha y horario. Sin superposiciones, sin errores, cronograma visual del día.
                </p>
            </div>

            <div class="feature-card relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-brand-200 hover:shadow-lg transition-all duration-200 reveal" style="transition-delay:0.15s">
                <div class="w-11 h-11 bg-brand-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <h3 class="font-display text-base font-bold text-gray-900 uppercase tracking-wide mb-2">Resultados en Vivo</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Cargá sets y games en tiempo real. El bracket avanza automáticamente y los jugadores lo ven desde el celular.
                </p>
            </div>

            <div class="feature-card relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-accent-200 hover:shadow-lg transition-all duration-200 reveal" style="transition-delay:0.2s">
                <div class="w-11 h-11 bg-accent-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-accent-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                </div>
                <h3 class="font-display text-base font-bold text-gray-900 uppercase tracking-wide mb-2">Notificaciones</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Los jugadores reciben alertas de sus partidos, cancha asignada y resultados. Sin WhatsApp del organizador.
                </p>
            </div>

            <div class="feature-card relative bg-white rounded-2xl p-6 border border-gray-100 hover:border-green-200 hover:shadow-lg transition-all duration-200 reveal md:col-span-2 lg:col-span-1" style="transition-delay:0.25s">
                <div class="w-11 h-11 bg-green-50 rounded-xl flex items-center justify-center mb-4">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h3 class="font-display text-base font-bold text-gray-900 uppercase tracking-wide mb-2">Acceso desde el Celular</h3>
                <p class="text-gray-500 text-sm leading-relaxed">
                    Jugadores y organizadores acceden desde cualquier dispositivo. El torneo en el bolsillo de todos.
                </p>
            </div>

        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     PRICING
═══════════════════════════════════════════════ --}}
<section class="bg-white py-16 sm:py-20 lg:py-24 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <span class="inline-flex items-center gap-2 border-l-4 border-accent-600 pl-3 text-xs font-bold uppercase tracking-widest text-accent-600 mb-4">
                Precios
            </span>
            <h2 class="font-display text-4xl sm:text-5xl font-black text-gray-900 tracking-tight uppercase">
                Simple y sin sorpresas
            </h2>
            <p class="text-base sm:text-lg text-gray-500 max-w-md mx-auto mt-3">
                Pagás solo cuando organizás un torneo. Sin suscripciones, sin letra chica.
            </p>
        </div>

        <div class="max-w-3xl mx-auto grid md:grid-cols-2 gap-6 md:gap-4 items-stretch reveal">
            {{-- Free --}}
            <div class="bg-white rounded-3xl p-7 sm:p-8 border-2 border-gray-200 hover:border-brand-300 hover:shadow-lg transition-all duration-300 flex flex-col">
                <div class="mb-7">
                    <span class="inline-flex items-center gap-1.5 bg-green-50 text-green-700 border border-green-100 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide mb-5">
                        <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                        Gratis para empezar
                    </span>
                    <h3 class="font-display text-2xl font-bold text-gray-900 uppercase mb-3">Primer Torneo</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="font-display text-5xl font-black text-gray-900">$0</span>
                    </div>
                    <p class="text-gray-400 text-sm mt-1.5">Acceso completo, sin límites</p>
                </div>

                <ul class="space-y-3 mb-8 flex-1">
                    @foreach(['Parejas ilimitadas', 'Todas las funcionalidades', 'Notificaciones por email', 'Gestión de llaves y resultados', 'Acceso web para jugadores'] as $item)
                    <li class="flex items-center gap-3 text-sm text-gray-600">
                        <span class="w-5 h-5 bg-green-50 rounded-full flex items-center justify-center flex-shrink-0 border border-green-100">
                            <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>

                <a href="{{ route('register') }}"
                    class="block w-full bg-gray-900 text-white text-center px-6 py-3.5 rounded-xl font-semibold text-sm hover:bg-gray-700 transition duration-200">
                    Comenzar Gratis
                </a>
            </div>

            {{-- Paid --}}
            @php $precioTorneo = \App\Models\ConfiguracionSistema::get('precio_torneo', 25000); @endphp
            <div class="relative bg-brand-900 rounded-3xl p-7 sm:p-8 border-2 border-brand-700/50 shadow-2xl shadow-brand-200/40 md:scale-[1.03] flex flex-col">
                <div class="absolute -top-4 inset-x-0 flex justify-center pointer-events-none">
                    <span class="bg-accent-600 text-white px-5 py-1.5 rounded-full text-[0.7rem] font-black tracking-widest uppercase shadow-lg">
                        ★ Más popular
                    </span>
                </div>

                <div class="mb-7 mt-2">
                    <h3 class="font-display text-2xl font-bold text-white uppercase mb-3">Por Torneo</h3>
                    <div class="flex items-baseline gap-1">
                        <span class="font-display text-5xl font-black text-white">${{ number_format($precioTorneo, 0, ',', '.') }}</span>
                    </div>
                    <p class="text-brand-300/80 text-sm mt-1.5">por cada torneo adicional</p>
                </div>

                <ul class="space-y-3 mb-8 flex-1">
                    @foreach(['Todo lo del plan gratis', 'Pago único por torneo', 'Sin suscripciones mensuales', 'Soporte prioritario', 'Historial ilimitado'] as $item)
                    <li class="flex items-center gap-3 text-sm text-white/80">
                        <span class="w-5 h-5 bg-white/15 rounded-full flex items-center justify-center flex-shrink-0">
                            <svg class="w-2.5 h-2.5 text-accent-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        {{ $item }}
                    </li>
                    @endforeach
                </ul>

                <a href="{{ route('register') }}"
                    class="block w-full bg-accent-600 text-white text-center px-6 py-3.5 rounded-xl font-bold text-sm hover:bg-accent-500 transition duration-200 shadow-lg">
                    Comenzar Ahora
                </a>
            </div>
        </div>

        <p class="mt-10 text-center text-gray-400 text-xs flex items-center justify-center gap-2">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            Pago seguro con Mercado Pago &nbsp;·&nbsp; Sin comisiones ocultas &nbsp;·&nbsp; Cancela cuando quieras
        </p>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     REFERRAL
═══════════════════════════════════════════════ --}}
<section class="bg-gray-50 py-16 sm:py-20 lg:py-24 border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-14 reveal">
            <span class="inline-flex items-center gap-2 border-l-4 border-green-500 pl-3 text-xs font-bold uppercase tracking-widest text-green-600 mb-4">
                Programa de referidos
            </span>
            <h2 class="font-display text-4xl sm:text-5xl font-black text-gray-900 tracking-tight uppercase">
                Invitá colegas,<br class="hidden sm:block"> ganá torneos gratis
            </h2>
            <p class="text-base sm:text-lg text-gray-500 max-w-xl mx-auto mt-3">
                Compartí PickleTorneos con otros organizadores de pickleball y acumulá beneficios
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-6 mb-10 max-w-4xl mx-auto">
            {{-- Para el referido --}}
            <div class="bg-white rounded-3xl p-7 sm:p-8 border-2 border-gray-200 hover:border-brand-300 hover:shadow-lg transition-all duration-300 reveal">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-brand-50 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-lg font-bold text-gray-900 uppercase tracking-wide">Para vos (Nuevo)</h3>
                        <p class="text-brand-600 text-sm font-medium">Al registrarte con un código</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-200">
                            <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Primer torneo GRATIS</p>
                            <p class="text-gray-500 text-xs mt-0.5">Sin pagar nada, acceso completo</p>
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
                            <p class="font-semibold text-gray-900 text-sm">{{ $porcentajeDescuento }}% de descuento en el segundo</p>
                            <p class="text-gray-500 text-xs mt-0.5">Ahorrás ${{ number_format($montoDescuento, 0, ',', '.') }} en tu primer pago</p>
                        </div>
                    </div>
                    <div class="bg-brand-50 rounded-2xl p-4 mt-5 border border-brand-100">
                        <p class="text-brand-900 font-bold text-sm text-center">
                            Total ahorrado: ${{ number_format($precioTorneo + $montoDescuento, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Para el referidor --}}
            <div class="bg-white rounded-3xl p-7 sm:p-8 border-2 border-gray-200 hover:border-green-300 hover:shadow-lg transition-all duration-300 reveal" style="transition-delay:0.1s">
                <div class="flex items-center gap-4 mb-6">
                    <div class="w-12 h-12 bg-green-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-display text-lg font-bold text-gray-900 uppercase tracking-wide">Para vos (Referidor)</h3>
                        <p class="text-green-600 text-sm font-medium">Por cada colega que invités</p>
                    </div>
                </div>

                <div class="space-y-4">
                    @foreach(['1 Torneo Gratis por cada referido — cuando tu referido pague su primer torneo', 'Sin límite de referidos — invitá a todos los que quieras', 'Créditos válidos por 12 meses — usálos cuando quieras'] as $item)
                    <div class="flex items-start gap-3">
                        <span class="w-5 h-5 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 border border-green-200">
                            <svg class="w-2.5 h-2.5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                        </span>
                        <p class="text-gray-600 text-sm">{{ $item }}</p>
                    </div>
                    @endforeach
                    <div class="bg-green-50 rounded-2xl p-4 mt-5 border border-green-100">
                        <p class="text-green-900 font-bold text-sm text-center">
                            Valor por referido: ${{ number_format($precioTorneo, 0, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- CTA referidos --}}
        <div class="bg-brand-800 rounded-3xl p-8 sm:p-12 text-center shadow-2xl max-w-4xl mx-auto reveal">
            <h3 class="font-display text-3xl sm:text-4xl font-black text-white uppercase tracking-tight mb-3">
                ¿Listo para ganar torneos gratis?
            </h3>
            <p class="text-brand-200/80 text-base mb-8 max-w-xl mx-auto">
                Registrate ahora y recibí tu código único. Compartilo con colegas del mundo pickleball y acumulá créditos.
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('register') }}"
                   class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-accent-600 text-white font-bold rounded-xl hover:bg-accent-500 transition duration-200 shadow-lg text-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Crear mi Cuenta Gratis
                </a>
                @auth
                <a href="{{ route('referidos.dashboard') }}"
                   class="inline-flex items-center justify-center gap-2 px-7 py-3.5 bg-white/15 backdrop-blur-sm border border-white/25 text-white font-semibold rounded-xl hover:bg-white/25 transition duration-200 text-sm">
                    Ver mi Dashboard de Referidos
                </a>
                @endauth
            </div>
        </div>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     FINAL CTA
═══════════════════════════════════════════════ --}}
<section class="relative text-white py-20 sm:py-24 lg:py-32 overflow-hidden"
    style="background-image: url('{{ asset('images/banner2.png') }}'); background-size: cover; background-position: center;">

    {{-- Teal dark overlay (NOT indigo) --}}
    <div class="absolute inset-0" style="background: linear-gradient(135deg, rgba(10,56,66,0.93) 0%, rgba(15,107,120,0.85) 100%);"></div>

    {{-- Decorative element --}}
    <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute bottom-0 left-0 w-64 h-64 bg-accent-600/10 rounded-full blur-3xl"></div>
        <div class="absolute top-0 right-0 w-96 h-96 bg-brand-500/10 rounded-full blur-3xl"></div>
    </div>

    <div class="relative max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/15 px-4 py-2 rounded-full text-xs font-bold uppercase tracking-widest text-white/80 mb-7">
            🏓 Unite a la comunidad pickleball
        </div>
        <h2 class="font-display text-4xl sm:text-5xl lg:text-6xl font-black uppercase tracking-tight leading-tight mb-5">
            ¿Listo para llevar tus torneos<br class="hidden sm:block"> al siguiente nivel?
        </h2>
        <p class="text-lg text-white/65 mb-10 max-w-xl mx-auto">
            Sumarte a los organizadores que ya confían en PickleTorneos para gestionar sus eventos de pickleball en Argentina.
        </p>
        <div class="flex flex-col sm:flex-row gap-3 justify-center">
            <a href="{{ route('register') }}"
                class="inline-flex items-center justify-center gap-2 bg-accent-600 text-white px-8 py-4 rounded-xl font-bold text-base hover:bg-accent-500 transition-all duration-200 shadow-2xl hover:scale-[1.02]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Crear Torneo Gratis
            </a>
            <a href="{{ route('login') }}"
                class="inline-flex items-center justify-center bg-white/10 backdrop-blur-sm border border-white/20 text-white px-8 py-4 rounded-xl font-semibold text-base hover:bg-white/20 transition-all duration-200">
                Ya tengo cuenta
            </a>
        </div>
        <p class="mt-8 text-xs text-white/40">
            Primer torneo completamente gratis &nbsp;·&nbsp; Sin pagos mensuales &nbsp;·&nbsp; Soporte incluido
        </p>
    </div>
</section>


{{-- ═══════════════════════════════════════════════
     FOOTER
═══════════════════════════════════════════════ --}}
<footer class="bg-gray-950 text-gray-400 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col items-center gap-6">
            <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-9 opacity-80">
            <p class="text-gray-500 text-sm">Plataforma profesional de gestión de torneos de Pickleball</p>

            <div class="flex items-center gap-4">
                <a href="mailto:pickletorneossde@gmail.com"
                    class="w-9 h-9 bg-gray-800 hover:bg-brand-700 rounded-lg flex items-center justify-center transition-colors duration-200 group" title="pickletorneossde@gmail.com">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </a>
                <a href="https://wa.me/543857477092" target="_blank"
                    class="w-9 h-9 bg-gray-800 hover:bg-green-600 rounded-lg flex items-center justify-center transition-colors duration-200 group" title="+54 385 747 7092">
                    <svg class="w-4 h-4 text-gray-400 group-hover:text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                    </svg>
                </a>
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
                © {{ date('Y') }} PickleTorneos. Todos los derechos reservados.
            </p>
        </div>
    </div>
</footer>

{{-- WhatsApp float --}}
<a href="https://wa.me/543857477092" target="_blank"
    class="fixed bottom-6 right-6 z-50 w-14 h-14 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center shadow-lg shadow-green-500/40 hover:scale-110 transition-all duration-200"
    title="Contactar por WhatsApp">
    <svg class="w-7 h-7 text-white" fill="currentColor" viewBox="0 0 24 24">
        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
</a>

{{-- Scroll reveal script --}}
<script>
    const reveals = document.querySelectorAll('.reveal');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
            }
        });
    }, { threshold: 0.1 });
    reveals.forEach(el => observer.observe(el));
</script>

@endsection
