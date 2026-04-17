@extends('layouts.dashboard')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')

@section('content')
<div class="max-w-4xl mx-auto space-y-4 sm:space-y-6">
    <!-- Tarjeta de Información Personal -->
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-700 to-brand-500 px-4 sm:px-6 py-4">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4">
                <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-full bg-white flex items-center justify-center flex-shrink-0">
                    <span class="text-3xl sm:text-4xl font-bold text-brand-600">
                        {{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}
                    </span>
                </div>
                <div class="flex-1 text-center sm:text-left">
                    <h2 class="text-xl sm:text-2xl font-bold text-white">
                        {{ auth()->user()->name }} {{ auth()->user()->apellido }}
                    </h2>
                    <p class="text-brand-100 mt-1 text-sm sm:text-base">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->organizacion)
                        <div class="mt-2 inline-flex items-center px-3 py-1 bg-white bg-opacity-20 text-white rounded-full text-xs sm:text-sm">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd"></path>
                            </svg>
                            {{ auth()->user()->organizacion }}
                        </div>
                    @endif
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('profile.edit') }}" class="inline-flex items-center px-3 sm:px-4 py-2 bg-white text-brand-600 rounded-lg hover:bg-brand-50 transition font-semibold text-xs sm:text-sm">
                        <svg class="w-4 h-4 mr-1 sm:mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Editar
                    </a>
                </div>
            </div>
        </div>

        <!-- Información Detallada -->
        <div class="p-4 sm:p-6 space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                <!-- Nombre -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Nombre</label>
                    <p class="text-gray-900 font-medium text-sm sm:text-base">{{ auth()->user()->name }}</p>
                </div>

                <!-- Apellido -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Apellido</label>
                    <p class="text-gray-900 font-medium text-sm sm:text-base">{{ auth()->user()->apellido ?? 'No especificado' }}</p>
                </div>

                <!-- Email -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Email</label>
                    <p class="text-gray-900 font-medium text-sm sm:text-base break-all">{{ auth()->user()->email }}</p>
                </div>

                <!-- Teléfono -->
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Teléfono</label>
                    <p class="text-gray-900 font-medium text-sm sm:text-base">{{ auth()->user()->telefono ?? 'No especificado' }}</p>
                </div>

                <!-- Organización -->
                @if(auth()->user()->organizacion)
                    <div class="sm:col-span-2">
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-1">Organización</label>
                        <p class="text-gray-900 font-medium text-sm sm:text-base">{{ auth()->user()->organizacion }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tarjeta de Seguridad -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h3 class="text-base sm:text-lg font-semibold text-gray-800">Seguridad</h3>
                <p class="text-xs sm:text-sm text-gray-600 mt-1">Administra tu contraseña y seguridad de la cuenta</p>
            </div>
        </div>

        @if(auth()->user()->google_id)
            <div class="flex items-center gap-3 p-4 bg-blue-50 border border-brand-200 rounded-lg">
                <svg class="w-8 h-8 flex-shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Cuenta conectada con Google</p>
                    <p class="text-xs text-blue-700 mt-0.5">Tu acceso está gestionado por Google. No necesitás contraseña.</p>
                </div>
            </div>
        @else
            <div class="flex flex-col sm:flex-row sm:items-center justify-between p-4 bg-gray-50 rounded-lg gap-3">
                <div class="flex items-start sm:items-center gap-3">
                    <div class="p-2 bg-brand-100 rounded-lg flex-shrink-0">
                        <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 text-sm sm:text-base">Contraseña</p>
                        <p class="text-xs sm:text-sm text-gray-600">••••••••</p>
                    </div>
                </div>
                <a href="{{ route('profile.password.edit') }}" class="inline-flex items-center justify-center px-4 py-2 border border-brand-600 text-brand-600 rounded-lg hover:bg-brand-50 transition font-semibold text-xs sm:text-sm">
                    Cambiar Contraseña
                </a>
            </div>
        @endif
    </div>

    <!-- Tarjeta de Estadísticas -->
    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6">
        <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-4">Estadísticas</h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <!-- Torneos -->
            <div class="bg-gradient-to-br from-brand-50 to-brand-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-brand-600 font-medium uppercase tracking-wider mb-1">Torneos</p>
                        <p class="text-2xl sm:text-3xl font-bold text-brand-900">{{ auth()->user()->torneos_creados }}</p>
                    </div>
                    <div class="p-3 bg-brand-200 rounded-lg">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Complejos -->
            <div class="bg-gradient-to-br from-brand-50 to-brand-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-brand-600 font-medium uppercase tracking-wider mb-1">Complejos</p>
                        <p class="text-2xl sm:text-3xl font-bold text-purple-900">{{ auth()->user()->complejos->count() }}</p>
                    </div>
                    <div class="p-3 bg-purple-200 rounded-lg">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Rol -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-lg p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-xs text-green-600 font-medium uppercase tracking-wider mb-1">Rol</p>
                        <p class="text-base sm:text-lg font-bold text-green-900">{{ auth()->user()->roles->first()->name ?? 'Usuario' }}</p>
                    </div>
                    <div class="p-3 bg-green-200 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
