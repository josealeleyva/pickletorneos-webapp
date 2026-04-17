@extends('layouts.app')

@section('title', 'Tutoriales - PickleTorneos')

@section('content')
    <!-- Header Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50">
        <div class="bg-white/95 backdrop-blur-md border-b border-gray-100/80 shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-16">
                    <!-- Logo -->
                    <div class="flex items-center">
                        <a href="{{ route('landing') }}">
                            <img src="{{ asset('images/logo-color.png') }}" alt="PickleTorneos" class="h-8 sm:h-9">
                        </a>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="hidden sm:flex items-center gap-2">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="text-gray-600 hover:text-brand-600 font-medium text-sm px-3 py-2 rounded-lg hover:bg-brand-50 transition duration-150">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-gray-600 hover:text-brand-600 font-medium text-sm px-3 py-2 rounded-lg hover:bg-brand-50 transition duration-150">
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}"
                                class="bg-brand-600 text-white px-5 py-2 rounded-lg font-semibold text-sm hover:bg-brand-700 transition duration-150 shadow-md shadow-brand-100 ml-1">
                                Registrarse gratis
                            </a>
                        @endauth
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
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="text-gray-600 font-medium text-sm py-2.5 px-3 rounded-lg hover:bg-gray-50 transition">
                                Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-gray-600 font-medium text-sm py-2.5 px-3 rounded-lg hover:bg-gray-50 transition">
                                Iniciar Sesión
                            </a>
                            <a href="{{ route('register') }}"
                                class="bg-brand-600 text-white px-4 py-3 rounded-xl font-semibold text-sm hover:bg-brand-700 transition text-center mt-2 shadow-md shadow-brand-100">
                                Registrarse gratis
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="relative text-white pt-24 pb-16 sm:pt-28 sm:pb-20 overflow-hidden"
        style="background-image: url('{{ asset('images/banner2.png') }}'); background-size: cover; background-position: center;">
        <div class="absolute inset-0" style="background-color: rgba(30, 27, 75, 0.82);"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="flex justify-center mb-6">
                <div class="w-16 h-16 sm:w-20 sm:h-20 bg-white/10 backdrop-blur-sm border border-white/20 rounded-2xl flex items-center justify-center">
                    <svg class="w-8 h-8 sm:w-10 sm:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight mb-4">Centro de Tutoriales</h1>
            <p class="text-base sm:text-lg text-white/75 max-w-xl mx-auto">
                Aprende a usar PickleTorneos con nuestros videos paso a paso
            </p>
        </div>
    </div>

    <!-- Tutoriales Section -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 sm:py-16">
        @php
            $allVideos = config('tutoriales.videos');
            $categorias = config('tutoriales.categorias');

            // Organizar videos manualmente por categoría preservando keys
            $videosPorCategoria = [];
            foreach ($allVideos as $key => $video) {
                $cat = $video['category'];
                if (!isset($videosPorCategoria[$cat])) {
                    $videosPorCategoria[$cat] = [];
                }
                $videosPorCategoria[$cat][$key] = $video;
            }

            // Ordenar categorías por orden
            uksort($videosPorCategoria, function($a, $b) use ($categorias) {
                $orderA = $categorias[$a]['order'] ?? 999;
                $orderB = $categorias[$b]['order'] ?? 999;
                return $orderA <=> $orderB;
            });

            // Ordenar videos dentro de cada categoría preservando keys
            foreach ($videosPorCategoria as $cat => &$videos) {
                uasort($videos, function($a, $b) {
                    return ($a['order'] ?? 999) <=> ($b['order'] ?? 999);
                });
            }
        @endphp

        @foreach($videosPorCategoria as $categoriaKey => $videosCategoria)
            @php
                $categoria = $categorias[$categoriaKey] ?? null;
                if (!$categoria) continue;

                $colorClasses = [
                    'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
                    'indigo' => 'bg-brand-100 text-brand-800 border-brand-200',
                    'green' => 'bg-green-100 text-green-800 border-green-200',
                ];
                $colorClass = $colorClasses[$categoria['color']] ?? $colorClasses['blue'];
            @endphp

            <!-- Categoría Header -->
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="flex items-center justify-center w-12 h-12 rounded-lg {{ $colorClass }} border">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $categoria['icono'] }}"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl sm:text-3xl font-bold text-gray-900">{{ $categoria['nombre'] }}</h2>
                </div>

                <!-- Grid de Videos -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($videosCategoria as $videoKey => $video)
                        <div class="bg-white rounded-2xl border border-gray-100 hover:border-brand-100 hover:shadow-lg transition-all duration-200 overflow-hidden">
                            <!-- Video Preview -->
                            <div class="relative bg-gray-900 aspect-video">
                                <video
                                    id="video-{{ $videoKey }}"
                                    class="w-full h-full object-cover"
                                    preload="metadata"
                                    poster="{{ asset('images/logo-color.png') }}"
                                >
                                    <source src="{{ asset('storage/videos/tutoriales/' . $video['filename']) }}" type="video/mp4">
                                    Tu navegador no soporta videos HTML5.
                                </video>

                                <!-- Play Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-40 hover:bg-opacity-30 transition-all cursor-pointer" onclick="toggleVideo('{{ $videoKey }}')">
                                    <div id="play-button-{{ $videoKey }}" class="w-16 h-16 rounded-full bg-white bg-opacity-90 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-brand-600 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path>
                                        </svg>
                                    </div>
                                    <div id="pause-button-{{ $videoKey }}" class="w-16 h-16 rounded-full bg-white bg-opacity-90 flex items-center justify-center hidden">
                                        <svg class="w-8 h-8 text-brand-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M5 4a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V4zM13 4a2 2 0 012-2h2a2 2 0 012 2v12a2 2 0 01-2 2h-2a2 2 0 01-2-2V4z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Video Info -->
                            <div class="p-5">
                                <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $video['title'] }}</h3>
                                <p class="text-sm text-gray-600 mb-4">{{ $video['description'] }}</p>

                                <!-- Actions -->
                                <div class="flex gap-2">
                                    <button
                                        onclick="toggleVideo('{{ $videoKey }}')"
                                        class="flex-1 bg-brand-600 hover:bg-brand-700 text-white px-4 py-2 rounded-lg font-semibold transition text-sm flex items-center justify-center gap-2"
                                    >
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path>
                                        </svg>
                                        <span id="play-text-{{ $videoKey }}">Reproducir</span>
                                        <span id="pause-text-{{ $videoKey }}" class="hidden">Pausar</span>
                                    </button>
                                    <button
                                        onclick="openFullscreen('{{ $videoKey }}')"
                                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-semibold transition text-sm"
                                        title="Pantalla completa"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- CTA Section -->
        <div class="mt-16 bg-brand-800 rounded-3xl p-8 sm:p-12 text-center text-white shadow-2xl shadow-brand-100">
            <h2 class="text-2xl sm:text-3xl font-black tracking-tight mb-3">¿Listo para comenzar?</h2>
            <p class="text-base text-brand-100/80 mb-8 max-w-xl mx-auto">
                Ahora que conoces cómo funciona la plataforma, crea tu cuenta y comienza a organizar torneos profesionales
            </p>
            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                @auth
                    <a href="{{ route('dashboard') }}" class="inline-flex items-center justify-center bg-white text-brand-700 px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-accent-50 transition shadow-lg">
                        Ir al Dashboard
                    </a>
                @else
                    <a href="{{ route('register') }}" class="inline-flex items-center justify-center bg-white text-brand-700 px-8 py-3.5 rounded-xl font-bold text-sm hover:bg-accent-50 transition shadow-lg">
                        Registrarse Gratis
                    </a>
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center bg-white/15 backdrop-blur-sm border border-white/25 text-white px-8 py-3.5 rounded-xl font-semibold text-sm hover:bg-white/25 transition">
                        Iniciar Sesión
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-gray-950 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col items-center gap-6">
                <img src="{{ asset('images/logo-blanco.png') }}" alt="PickleTorneos" class="h-9 opacity-80">
                <p class="text-gray-500 text-sm">Sistema profesional de gestión de torneos deportivos</p>

                <!-- Redes sociales -->
                <div class="flex items-center gap-4">
                    <a href="mailto:pickletorneossde@gmail.com"
                        class="w-9 h-9 bg-gray-800 hover:bg-brand-600 rounded-lg flex items-center justify-center transition-colors duration-200 group" title="pickletorneossde@gmail.com">
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
                    <a href="{{ route('landing') }}" class="hover:text-gray-400 transition duration-200">Inicio</a>
                    <a href="{{ route('tyc') }}" class="hover:text-gray-400 transition duration-200">Términos y Condiciones</a>
                </div>
                <p class="text-xs text-gray-700 pt-2 border-t border-gray-800/60 w-full text-center">
                    © {{ date('Y') }} PickleTorneos. Todos los derechos reservados.
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

    <!-- JavaScript -->
    <script>
        // Toggle mobile menu
        document.getElementById('mobile-menu-button')?.addEventListener('click', function() {
            const menu = document.getElementById('mobile-menu');
            const hamburger = document.getElementById('hamburger-icon');
            const close = document.getElementById('close-icon');
            menu.classList.toggle('hidden');
            hamburger?.classList.toggle('hidden');
            close?.classList.toggle('hidden');
        });

        // Pausar todos los videos excepto el actual
        function pauseAllVideosExcept(currentVideoKey) {
            document.querySelectorAll('video').forEach(video => {
                if (video.id !== 'video-' + currentVideoKey && !video.paused) {
                    video.pause();
                    const key = video.id.replace('video-', '');
                    updateVideoUI(key, false);
                }
            });
        }

        // Actualizar UI del video (botones y textos)
        function updateVideoUI(videoKey, isPlaying) {
            const playButton = document.getElementById('play-button-' + videoKey);
            const pauseButton = document.getElementById('pause-button-' + videoKey);
            const playText = document.getElementById('play-text-' + videoKey);
            const pauseText = document.getElementById('pause-text-' + videoKey);

            if (isPlaying) {
                playButton?.classList.add('hidden');
                pauseButton?.classList.remove('hidden');
                playText?.classList.add('hidden');
                pauseText?.classList.remove('hidden');
            } else {
                playButton?.classList.remove('hidden');
                pauseButton?.classList.add('hidden');
                playText?.classList.remove('hidden');
                pauseText?.classList.add('hidden');
            }
        }

        // Video controls
        function toggleVideo(videoKey) {
            const video = document.getElementById('video-' + videoKey);

            if (!video) {
                console.error('Video no encontrado:', videoKey);
                return;
            }

            if (video.paused) {
                // Pausar todos los demás videos
                pauseAllVideosExcept(videoKey);

                // Reproducir este video
                video.play().catch(err => {
                    console.error('Error reproduciendo video:', err);
                });
                updateVideoUI(videoKey, true);
            } else {
                // Pausar este video
                video.pause();
                updateVideoUI(videoKey, false);
            }
        }

        function openFullscreen(videoKey) {
            const video = document.getElementById('video-' + videoKey);

            if (!video) return;

            // Pausar otros videos
            pauseAllVideosExcept(videoKey);

            // Abrir en pantalla completa
            if (video.requestFullscreen) {
                video.requestFullscreen();
            } else if (video.webkitRequestFullscreen) {
                video.webkitRequestFullscreen();
            } else if (video.msRequestFullscreen) {
                video.msRequestFullscreen();
            }

            // Reproducir
            video.play().catch(err => {
                console.error('Error reproduciendo video:', err);
            });
            updateVideoUI(videoKey, true);
        }

        // Reset play buttons when video ends
        document.querySelectorAll('video').forEach(video => {
            // Al terminar el video
            video.addEventListener('ended', function() {
                const videoKey = this.id.replace('video-', '');
                updateVideoUI(videoKey, false);
            });

            // Al pausar el video manualmente
            video.addEventListener('pause', function() {
                const videoKey = this.id.replace('video-', '');
                updateVideoUI(videoKey, false);
            });

            // Al reproducir el video manualmente
            video.addEventListener('play', function() {
                const videoKey = this.id.replace('video-', '');
                pauseAllVideosExcept(videoKey);
                updateVideoUI(videoKey, true);
            });
        });
    </script>
@endsection
