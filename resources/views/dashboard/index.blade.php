@extends('layouts.dashboard')

@section('title', 'Dashboard')
@section('page-title', 'Bienvenido al Dashboard')

@section('content')
<div class="space-y-4 sm:space-y-6">
    <!-- Welcome Card -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 border-l-4 border-brand-600">
        <h3 class="text-lg sm:text-xl font-semibold text-gray-800 mb-2">
            ¡Hola, {{ auth()->user()->name }}! 👋
        </h3>
        <p class="text-gray-600 text-sm sm:text-base">
            Bienvenido a tu panel de gestión de torneos. Desde aquí podrás administrar todos tus eventos deportivos.
        </p>

        <div class="mt-4 flex flex-wrap items-center gap-2">
            <div class="inline-flex items-center px-3 py-1 {{ auth()->user()->cuenta_activa ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }} rounded-full text-xs font-semibold">
                <span class="w-3 h-3 {{ auth()->user()->cuenta_activa ? 'bg-green-500' : 'bg-red-500' }} rounded-full mr-2"></span>
                {{ auth()->user()->cuenta_activa ? 'Cuenta Activa' : 'Cuenta Inactiva' }}
            </div>

            @if(auth()->user()->organizacion)
                <div class="inline-flex items-center px-3 py-1 bg-brand-100 text-brand-800 rounded-full text-xs">
                    <svg class="w-3 h-3 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                    </svg>
                    {{ auth()->user()->organizacion }}
                </div>
            @endif
        </div>
    </div>

    <!-- Info Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
        <!-- Torneos Card -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Mis Torneos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ $torneosBorrador + $torneosActivos + $torneosFinalizados }}</p>
                </div>
                <div class="p-3 bg-brand-100 rounded-full">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-200">
                <!-- Borrador -->
                <div class="relative group flex-1 text-center cursor-help">
                    <div class="flex flex-col items-center p-2 rounded-lg hover:bg-yellow-50 transition">
                        <p class="text-xl sm:text-2xl font-bold text-yellow-600">{{ $torneosBorrador }}</p>
                        <div class="w-2 h-2 bg-yellow-400 rounded-full mt-1"></div>
                    </div>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 group-active:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-10">
                        Borrador
                    </div>
                </div>

                <!-- Activos -->
                <div class="relative group flex-1 text-center cursor-help">
                    <div class="flex flex-col items-center p-2 rounded-lg hover:bg-green-50 transition">
                        <p class="text-xl sm:text-2xl font-bold text-green-600">{{ $torneosActivos }}</p>
                        <div class="w-2 h-2 bg-green-400 rounded-full mt-1"></div>
                    </div>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 group-active:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-10">
                        Activos
                    </div>
                </div>

                <!-- Finalizados -->
                <div class="relative group flex-1 text-center cursor-help">
                    <div class="flex flex-col items-center p-2 rounded-lg hover:bg-brand-50 transition">
                        <p class="text-xl sm:text-2xl font-bold text-brand-600">{{ $torneosFinalizados }}</p>
                        <div class="w-2 h-2 bg-brand-400 rounded-full mt-1"></div>
                    </div>
                    <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 px-3 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 group-active:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-10">
                        Finalizados
                    </div>
                </div>
            </div>

            @if(auth()->user()->torneos_creados == 0)
                <div class="mt-4 pt-4 border-t border-gray-200">
                    <p class="text-xs text-green-600 font-semibold">✨ Tu primer torneo es GRATIS</p>
                </div>
            @endif
        </div>

        <!-- Complejos Card -->
        <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm text-gray-600 mb-1">Complejos Deportivos</p>
                    <p class="text-2xl sm:text-3xl font-bold text-gray-800">{{ auth()->user()->complejos->count() }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-8 h-8 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
            </div>

            @if(auth()->user()->complejos->count() > 0)
                <div class="space-y-2 mb-4">
                    @foreach(auth()->user()->complejos->take(2) as $complejo)
                        <div class="flex items-center text-sm text-gray-600 bg-gray-50 rounded px-3 py-2">
                            <svg class="w-4 h-4 mr-2 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span class="truncate">{{ $complejo->nombre }}</span>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="pt-4 border-t border-gray-200">
                <a href="{{ route('complejos.index') }}" class="text-sm text-brand-600 hover:text-brand-700 font-medium">
                    {{ auth()->user()->complejos->count() > 0 ? 'Ver todos los complejos →' : 'Crear primer complejo →' }}
                </a>
            </div>
        </div>
    </div>    
</div>
@endsection
