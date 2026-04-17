<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mi Panel') - PickleTorneos</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-icon.ico') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:  '#eef9fa',
              100: '#d5f0f3',
              200: '#aee2e8',
              300: '#78ccd6',
              400: '#42b0bf',
              500: '#1f95a6',
              600: '#147a8a',
              700: '#0F6B78',
              800: '#0d5764',
              900: '#0d4855',
              950: '#093038',
            },
            accent: {
              50:  '#fff4ec',
              100: '#ffe8d5',
              200: '#ffd0aa',
              300: '#ffb47a',
              400: '#ff9240',
              500: '#ff7a1a',
              600: '#FF6A00',
              700: '#d95800',
              800: '#b54800',
              900: '#8f3900',
              950: '#5a2200',
            }
          }
        }
      }
    }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col lg:flex-row">

        <!-- Mobile Header -->
        <header class="lg:hidden bg-brand-900 text-white sticky top-0 z-50">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-8">
                    <p class="text-brand-300 text-xs">Mi Panel</p>
                </div>
                <div class="flex items-center gap-1">
                    @include('partials._campana-notificaciones')
                    <button id="mobile-menu-button" class="p-2 rounded-lg hover:bg-brand-800 focus:outline-none focus:ring-2 focus:ring-brand-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Overlay -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 lg:hidden hidden z-30"></div>

        <!-- Sidebar -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 lg:z-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out w-64 bg-brand-900 text-white flex-shrink-0 lg:flex flex-col">
            <div class="relative flex flex-col h-full bg-brand-900">

                <!-- Desktop Logo -->
                <div class="hidden lg:block p-6">
                    <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-12 mb-2">
                    <p class="text-brand-300 text-sm mt-1">Mi Panel</p>
                </div>

                <!-- Mobile close button -->
                <div class="lg:hidden flex items-center justify-between p-4 border-b border-brand-800">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-10">
                        <p class="text-brand-300 text-xs">Mi Panel</p>
                    </div>
                    <button id="mobile-menu-close" class="p-2 rounded-lg hover:bg-brand-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto mt-6 lg:mt-0">

                    <!-- Mi Panel -->
                    <a href="{{ route('jugador.dashboard') }}"
                        class="flex items-center px-6 py-3 {{ request()->routeIs('jugador.dashboard') ? 'bg-brand-800 border-l-4 border-brand-400' : 'hover:bg-brand-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Mi Panel
                    </a>

                    <!-- Mis Torneos -->
                    <div class="px-6 py-2 mt-4 text-xs font-semibold text-brand-400 uppercase">
                        Mi Actividad
                    </div>

                    <a href="{{ route('jugador.torneos') }}"
                        class="flex items-center px-6 py-3 {{ request()->routeIs('jugador.torneos*') ? 'bg-brand-800 border-l-4 border-brand-400' : 'hover:bg-brand-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Mis Torneos
                    </a>

                    {{-- Mis Partidos --}}
                    @php
                        $partidosBadge = 0;
                        if (auth()->user()->jugador) {
                            $equipoIds = auth()->user()->jugador->equipos()->pluck('equipos.id');
                            $partidosBadge = \App\Models\ResultadoTentativo::whereHas('partido', function ($q) use ($equipoIds) {
                                $q->where(function ($q2) use ($equipoIds) {
                                    $q2->whereIn('equipo1_id', $equipoIds)
                                        ->orWhereIn('equipo2_id', $equipoIds);
                                });
                            })->whereNotIn('propuesto_por_equipo_id', $equipoIds)->count();
                        }
                    @endphp
                    <a href="{{ route('jugador.partidos') }}"
                        class="flex items-center px-6 py-3 {{ request()->routeIs('jugador.partidos*') ? 'bg-brand-800 border-l-4 border-brand-400' : 'hover:bg-brand-800' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                        <span class="flex-1">Mis Partidos</span>
                        @if($partidosBadge > 0)
                            <span class="bg-orange-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0">
                                {{ $partidosBadge > 9 ? '9+' : $partidosBadge }}
                            </span>
                        @endif
                    </a>

                    {{-- Inscripciones --}}
                    @php
                        $inscripcionesBadge = 0;
                        if (auth()->user()->jugador) {
                            $jugadorId = auth()->user()->jugador->id;
                            $inscripcionesBadge = \App\Models\InvitacionJugador::where('jugador_id', $jugadorId)
                                ->where('estado', 'pendiente')
                                ->count()
                                + \App\Models\InscripcionEquipo::where('lider_jugador_id', $jugadorId)
                                ->where('estado', 'pendiente')
                                ->count();
                        }
                    @endphp
                    <a href="{{ route('jugador.inscripciones') }}"
                        class="flex items-center px-6 py-3 {{ request()->routeIs('jugador.inscripciones*') ? 'bg-brand-800 border-l-4 border-brand-400' : 'hover:bg-brand-800' }}">
                        <svg class="w-5 h-5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                        <span class="flex-1">Inscripciones</span>
                        @if($inscripcionesBadge > 0)
                            <span class="bg-red-500 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0">
                                {{ $inscripcionesBadge > 9 ? '9+' : $inscripcionesBadge }}
                            </span>
                        @endif
                    </a>

                    <!-- Mi Perfil -->
                    <div class="px-6 py-2 mt-4 text-xs font-semibold text-brand-400 uppercase">
                        Mi Cuenta
                    </div>

                    <a href="{{ route('jugador.perfil') }}"
                        class="flex items-center px-6 py-3 {{ request()->routeIs('jugador.perfil*') ? 'bg-brand-800 border-l-4 border-brand-400' : 'hover:bg-brand-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Mi Perfil
                    </a>

                </nav>

                <!-- User info -->
                <div class="p-6 border-t border-brand-800">
                    <a href="{{ route('jugador.perfil') }}" class="flex items-center hover:bg-brand-800 p-2 rounded-lg transition -mx-2">
                        <div class="w-10 h-10 rounded-full bg-brand-600 flex items-center justify-center flex-shrink-0">
                            @if(auth()->user()->jugador?->foto)
                                <img src="{{ asset('storage/' . auth()->user()->jugador->foto) }}" alt="Foto" class="w-10 h-10 rounded-full object-cover">
                            @else
                                <span class="text-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}</span>
                            @endif
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ auth()->user()->name }} {{ auth()->user()->apellido }}</p>
                            <p class="text-xs text-brand-300 truncate">Jugador</p>
                        </div>
                        <svg class="w-4 h-4 text-brand-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm bg-brand-800 hover:bg-brand-700 rounded transition">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>

            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto w-full">
            <!-- Desktop Header -->
            <header class="hidden lg:block bg-white shadow-sm">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Mi Panel')</h2>
                    <div class="bg-brand-900 rounded-lg">
                        @include('partials._campana-notificaciones')
                    </div>
                </div>
            </header>

            <!-- Content -->
            <div class="p-4 md:p-6">
                @if(session('success'))
                    <div class="mb-4 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded text-sm">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                @yield('content')
            </div>
        </main>

    </div>

    <!-- Mobile Menu Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            const menuButton = document.getElementById('mobile-menu-button');
            const closeButton = document.getElementById('mobile-menu-close');

            function openMenu() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            }

            function closeMenu() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
                document.body.style.overflow = '';
            }

            if (menuButton) menuButton.addEventListener('click', function() {
                sidebar.classList.contains('-translate-x-full') ? openMenu() : closeMenu();
            });
            if (closeButton) closeButton.addEventListener('click', closeMenu);
            if (overlay) overlay.addEventListener('click', closeMenu);

            sidebar.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) closeMenu();
                });
            });

            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) closeMenu();
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
