<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Punto de Oro') - Sistema de Gestión de Torneos</title>
    <meta name="description" content="Plataforma integral para organizar y gestionar torneos de Padel, Fútbol y Tenis. Crea torneos con eliminación directa, fase de grupos o liga. Gestión de equipos, fixture automático, tabla de posiciones y pagos online.">
    <meta name="keywords" content="torneos, torneos deportivos, gestión de torneos, padel, fútbol, tenis, fixture automático, organizar torneo, eliminación directa, fase de grupos, liga, Argentina">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-icon.ico') }}">

    <!-- Tailwind CSS CDN (temporal - después lo puedes compilar con Vite) -->
    <script src="https://cdn.tailwindcss.com"></script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gray-100">
    @yield('content')

    @stack('scripts')
</body>
</html>
