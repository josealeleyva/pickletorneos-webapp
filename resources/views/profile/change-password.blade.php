@extends('layouts.dashboard')

@section('title', 'Cambiar Contraseña')
@section('page-title', 'Cambiar Contraseña')

@section('content')
<div class="max-w-2xl mx-auto">
    <!-- Breadcrumb -->
    <nav class="mb-4 sm:mb-6">
        <ol class="flex items-center space-x-2 text-xs sm:text-sm text-gray-600">
            <li>
                <a href="{{ route('profile.show') }}" class="hover:text-indigo-600">Mi Perfil</a>
            </li>
            <li>
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                </svg>
            </li>
            <li class="text-gray-900 font-medium">Cambiar Contraseña</li>
        </ol>
    </nav>

    <div class="bg-white rounded-lg shadow-sm p-4 sm:p-6 md:p-8">
        <!-- Info de seguridad -->
        <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-blue-600 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div>
                    <p class="text-sm font-semibold text-blue-900">Consejos de seguridad</p>
                    <ul class="text-xs text-blue-700 mt-2 space-y-1 list-disc list-inside">
                        <li>Usa al menos 8 caracteres</li>
                        <li>Combina letras mayúsculas y minúsculas</li>
                        <li>Incluye números y símbolos</li>
                        <li>No uses contraseñas fáciles de adivinar</li>
                    </ul>
                </div>
            </div>
        </div>

        <form action="{{ route('profile.password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <!-- Contraseña Actual -->
            <div class="mb-6">
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                    Contraseña Actual <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('current_password') border-red-500 @enderror"
                    placeholder="Tu contraseña actual"
                >
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Nueva Contraseña -->
            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                    Nueva Contraseña <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') border-red-500 @enderror"
                    placeholder="Mínimo 8 caracteres"
                >
                @error('password')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <!-- Confirmar Nueva Contraseña -->
            <div class="mb-6">
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                    Confirmar Nueva Contraseña <span class="text-red-500">*</span>
                </label>
                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                    placeholder="Repite tu nueva contraseña"
                >
            </div>

            <!-- Botones -->
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-3 sm:gap-4 pt-6 border-t border-gray-200">
                <a href="{{ route('profile.show') }}" class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition duration-200 text-center text-sm sm:text-base">
                    Cancelar
                </a>
                <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg shadow-md transition duration-200 text-sm sm:text-base">
                    Cambiar Contraseña
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
