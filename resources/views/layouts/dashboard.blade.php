<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - Punto de Oro</title>
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-icon.ico') }}">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col lg:flex-row">
        <!-- Mobile Header -->
        <header class="lg:hidden bg-indigo-900 text-white sticky top-0 z-50">
            <div class="flex items-center justify-between px-4 py-3">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-blanco.png') }}" alt="Punto de Oro" class="h-8">
                    <p class="text-indigo-300 text-xs">Gestión de Torneos</p>
                </div>
                <div class="flex items-center gap-1">
                    @include('partials._campana-notificaciones')
                    <button id="mobile-menu-button" class="p-2 rounded-lg hover:bg-indigo-800 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </header>

        <!-- Overlay for mobile -->
        <div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 lg:hidden hidden z-30"></div>

        <!-- Sidebar / Mobile Menu -->
        <aside id="sidebar" class="fixed lg:static inset-y-0 left-0 z-40 lg:z-0 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out w-64 bg-indigo-900 text-white flex-shrink-0 lg:flex flex-col">
            <!-- Sidebar content -->
            <div class="relative flex flex-col h-full bg-indigo-900">
                <!-- Desktop Logo -->
                <div class="hidden lg:block p-6">
                    <img src="{{ asset('images/logo-blanco.png') }}" alt="Punto de Oro" class="h-12 mb-2">
                    <p class="text-indigo-300 text-sm mt-1">Gestión de Torneos</p>
                </div>

                <!-- Mobile close button -->
                <div class="lg:hidden flex items-center justify-between p-4 border-b border-indigo-800">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset('images/logo-blanco.png') }}" alt="Punto de Oro" class="h-10">
                        <p class="text-indigo-300 text-xs">Gestión de Torneos</p>
                    </div>
                    <button id="mobile-menu-close" class="p-2 rounded-lg hover:bg-indigo-800">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto mt-6 lg:mt-0">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('dashboard') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Dashboard
                    </a>

                    <!-- Gestión -->
                    <div class="px-6 py-2 mt-6 text-xs font-semibold text-indigo-400 uppercase">
                        Gestión
                    </div>

                    <a href="{{ route('complejos.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('complejos.*') || request()->routeIs('canchas.*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                        Complejos
                    </a>

                    <a href="{{ route('categorias.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('categorias.*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Categorías
                    </a>

                    <a href="{{ route('jugadores.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('jugadores.*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        Jugadores
                    </a>

                    <a href="{{ route('torneos.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('torneos.*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Torneos
                    </a>

                    <!-- Programa de Referidos -->
                    <div class="px-6 py-2 mt-6 text-xs font-semibold text-indigo-400 uppercase">
                        Crecimiento
                    </div>

                    <a href="{{ route('referidos.dashboard') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('referidos.*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <div class="flex items-center justify-between flex-1">
                            <span>Mis Referidos</span>
                            @if(auth()->user()->cantidad_creditos > 0)
                                <span class="bg-green-500 text-white text-xs font-bold rounded-full px-2 py-0.5">
                                    {{ auth()->user()->cantidad_creditos }}
                                </span>
                            @endif
                        </div>
                    </a>

                    <!-- Soporte -->
                    <div class="px-6 py-2 mt-6 text-xs font-semibold text-indigo-400 uppercase">
                        Ayuda
                    </div>

                    <a href="{{ route('tutoriales') }}" class="flex items-center px-6 py-3 hover:bg-indigo-800">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        Tutoriales
                    </a>

                    <a href="{{ route('sugerencias.index') }}" class="flex items-center px-6 py-3 {{ request()->routeIs('sugerencias.*') ? 'bg-indigo-800 border-l-4 border-indigo-400' : 'hover:bg-indigo-800' }}">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                        </svg>
                        Sugerencias y Soporte
                    </a>
                </nav>

                <!-- User info -->
                <div class="p-6 border-t border-indigo-800">
                    <a href="{{ route('profile.show') }}" class="flex items-center hover:bg-indigo-800 p-2 rounded-lg transition -mx-2">
                        <div class="w-10 h-10 rounded-full bg-indigo-600 flex items-center justify-center flex-shrink-0">
                            <span class="text-sm font-bold">{{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}</span>
                        </div>
                        <div class="ml-3 flex-1 min-w-0">
                            <p class="text-sm font-medium truncate">{{ auth()->user()->name }} {{ auth()->user()->apellido }}</p>
                            <p class="text-xs text-indigo-300 truncate">{{ auth()->user()->roles->first()->name ?? 'Usuario' }}</p>
                        </div>
                        <svg class="w-4 h-4 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" class="mt-4">
                        @csrf
                        <button type="submit" class="w-full text-left px-4 py-2 text-sm bg-indigo-800 hover:bg-indigo-700 rounded transition">
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
                    <h2 class="text-2xl font-semibold text-gray-800">@yield('page-title', 'Dashboard')</h2>
                    <div class="bg-indigo-900 rounded-lg">
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

            function toggleMenu() {
                if (sidebar.classList.contains('-translate-x-full')) {
                    openMenu();
                } else {
                    closeMenu();
                }
            }

            if (menuButton) {
                menuButton.addEventListener('click', toggleMenu);
            }

            if (closeButton) {
                closeButton.addEventListener('click', closeMenu);
            }

            if (overlay) {
                overlay.addEventListener('click', closeMenu);
            }

            // Close menu on navigation (mobile)
            const navLinks = sidebar.querySelectorAll('a');
            navLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024) {
                        closeMenu();
                    }
                });
            });

            // Close menu on window resize to desktop
            window.addEventListener('resize', function() {
                if (window.innerWidth >= 1024) {
                    closeMenu();
                }
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
