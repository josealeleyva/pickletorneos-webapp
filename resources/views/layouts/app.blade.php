<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PickleTorneos') - Sistema de Gestión de Torneos</title>
    <meta name="description" content="Plataforma integral para organizar y gestionar torneos de Padel, Fútbol y Tenis. Crea torneos con eliminación directa, fase de grupos o liga. Gestión de equipos, fixture automático, tabla de posiciones y pagos online.">
    <meta name="keywords" content="torneos, torneos deportivos, gestión de torneos, padel, fútbol, tenis, fixture automático, organizar torneo, eliminación directa, fase de grupos, liga, Argentina">
    <link rel="icon" type="image/x-icon" href="{{ asset('images/logo-icon.ico') }}">

    <!-- Tailwind CSS CDN (temporal - después lo puedes compilar con Vite) -->
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

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @stack('styles')
</head>
<body class="bg-gray-100">
    @yield('content')

    @stack('scripts')
</body>
</html>
