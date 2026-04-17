@extends('layouts.app')

@section('title', 'Completar Perfil')

@section('content')
<div class="min-h-screen flex items-center justify-center py-8 sm:py-12 px-4">
    <div class="max-w-2xl w-full">
        <div class="bg-white rounded-lg shadow-2xl overflow-hidden">
            <!-- Header -->
            <div class="px-6 sm:px-8 pt-6 sm:pt-8 pb-6 text-center bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
                <div class="mb-4">
                    <img src="{{ asset('images/logo-blanco.png') }}" alt="Punto de Oro" class="h-16 mx-auto">
                </div>
                <h1 id="register-title" class="text-2xl sm:text-3xl font-bold">Completar Perfil</h1>
                <p id="register-subtitle" class="text-indigo-100 mt-2 text-sm sm:text-base">Últimos datos para crear tu cuenta</p>
            </div>

            @php $rolFijo = in_array($pending['rol'], ['jugador', 'organizador']); @endphp

            <!-- Switch Jugador / Organizador -->
            <div class="px-6 sm:px-8 pt-6">
                <div class="flex rounded-xl bg-gray-100 p-1 gap-1">
                    <button type="button" id="btn-jugador"
                        @if($rolFijo && $pending['rol'] !== 'jugador') disabled @endif
                        onclick="setTipo('jugador')"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $rolFijo && $pending['rol'] !== 'jugador' ? 'opacity-40 cursor-not-allowed' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Soy Jugador
                    </button>
                    <button type="button" id="btn-organizador"
                        @if($rolFijo && $pending['rol'] !== 'organizador') disabled @endif
                        onclick="setTipo('organizador')"
                        class="flex-1 flex items-center justify-center gap-2 py-2.5 rounded-lg text-sm font-semibold transition-all duration-200 {{ $rolFijo && $pending['rol'] !== 'organizador' ? 'opacity-40 cursor-not-allowed' : '' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        Soy Organizador
                    </button>
                </div>
            </div>

            <!-- Form -->
            <div class="px-6 sm:px-8 py-6 sm:py-8">

                <!-- Nota de Google -->
                <div class="mb-4 bg-blue-50 border border-blue-200 rounded-lg px-4 py-3 flex items-center gap-3">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" viewBox="0 0 24 24">
                        <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                        <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                        <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                        <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                    </svg>
                    <p class="text-sm text-blue-800">Estás creando tu cuenta con Google <span class="font-semibold">({{ $pending['email'] }})</span></p>
                </div>

                @if($errors->any())
                    <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded">
                        <ul class="list-disc list-inside text-sm">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @if(session('error'))
                    <div class="mb-4 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded text-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form method="POST" action="{{ route('auth.completar-perfil.store') }}" id="register-form">
                    @csrf
                    <input type="hidden" name="tipo_registro" id="tipo_registro" value="{{ old('tipo_registro', $pending['rol'] !== 'desconocido' ? $pending['rol'] : 'jugador') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Nombre -->
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Nombre *</label>
                            <input type="text" id="name" name="name" value="{{ old('name', $pending['nombre']) }}" required autofocus
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Juan">
                        </div>

                        <!-- Apellido -->
                        <div>
                            <label for="apellido" class="block text-sm font-medium text-gray-700 mb-2">Apellido *</label>
                            <input type="text" id="apellido" name="apellido" value="{{ old('apellido', $pending['apellido']) }}" required
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Pérez">
                        </div>

                        <!-- Email (readonly) -->
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" value="{{ $pending['email'] }}" readonly
                                class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 text-gray-500 cursor-not-allowed">
                        </div>

                        <!-- Teléfono -->
                        <div id="field-telefono">
                            <label for="telefono" class="block text-sm font-medium text-gray-700 mb-2">
                                Teléfono <span id="telefono-req">*</span>
                            </label>
                            <input type="text" id="telefono" name="telefono" value="{{ old('telefono') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="3416123456">
                        </div>

                        <!-- Deporte Principal (solo jugador) -->
                        <div id="field-deporte" class="hidden">
                            <label for="deporte_principal_id" class="block text-sm font-medium text-gray-700 mb-2">Deporte Principal</label>
                            <select id="deporte_principal_id" name="deporte_principal_id"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white">
                                <option value="">Seleccionar deporte</option>
                                @foreach($deportes as $deporte)
                                    <option value="{{ $deporte->id }}" {{ old('deporte_principal_id') == $deporte->id ? 'selected' : '' }}>
                                        {{ $deporte->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- DNI (solo jugador) -->
                        <div id="field-dni" class="hidden">
                            <label for="dni" class="block text-sm font-medium text-gray-700 mb-2">
                                DNI / CI <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="dni" name="dni" value="{{ old('dni') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('dni') border-red-500 @enderror"
                                placeholder="12345678">
                            @error('dni')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Fecha de Nacimiento (solo jugador) -->
                        <div id="field-fecha-nacimiento" class="hidden">
                            <label for="fecha_nacimiento" class="block text-sm font-medium text-gray-700 mb-2">
                                Fecha de Nacimiento <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="fecha_nacimiento" name="fecha_nacimiento"
                                value="{{ old('fecha_nacimiento') }}"
                                inputmode="numeric"
                                maxlength="10"
                                placeholder="DD/MM/AAAA"
                                autocomplete="off"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('fecha_nacimiento') border-red-500 @enderror">
                            @error('fecha_nacimiento')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Género (solo jugador) -->
                        <div id="field-genero" class="hidden">
                            <label for="genero" class="block text-sm font-medium text-gray-700 mb-2">
                                Género <span class="text-red-500">*</span>
                            </label>
                            <select id="genero" name="genero"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent bg-white @error('genero') border-red-500 @enderror">
                                <option value="">Seleccionar...</option>
                                <option value="masculino" {{ old('genero') == 'masculino' ? 'selected' : '' }}>Masculino</option>
                                <option value="femenino" {{ old('genero') == 'femenino' ? 'selected' : '' }}>Femenino</option>
                                <option value="otro" {{ old('genero') == 'otro' ? 'selected' : '' }}>Otro</option>
                            </select>
                            @error('genero')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Organización (solo organizador) -->
                        <div id="field-organizacion">
                            <label for="organizacion" class="block text-sm font-medium text-gray-700 mb-2">Organización</label>
                            <input type="text" id="organizacion" name="organizacion" value="{{ old('organizacion') }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                placeholder="Nombre de tu organización (opcional)">
                        </div>

                        <!-- Código de Referido (solo organizador) -->
                        <div id="field-referido" class="sm:col-span-2">
                            <label for="codigo_referido" class="block text-sm font-medium text-gray-700 mb-2">Código de Referido (Opcional)</label>
                            <input type="text" id="codigo_referido" name="codigo_referido"
                                value="{{ old('codigo_referido', request()->get('ref')) }}"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent uppercase"
                                placeholder="Ej: PO123ABC" maxlength="10">
                            @php
                                $porcentajeDescuento = \App\Models\ConfiguracionSistema::get('porcentaje_descuento_referido', 20);
                            @endphp
                            <p class="text-xs text-gray-500 mt-1">¿Tienes un código de referido? Ingresalo para obtener {{ $porcentajeDescuento }}% de descuento en tu primer torneo pago.</p>
                        </div>
                    </div>

                    <!-- Info box dinámica -->
                    <div id="info-jugador" class="mt-6 bg-green-50 border border-green-200 rounded-lg p-4 hidden">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-green-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-green-900">Registrate como jugador</p>
                                <p class="text-xs text-green-700 mt-1">Seguí tus partidos, torneos y estadísticas desde tu panel personal.</p>
                            </div>
                        </div>
                    </div>

                    <div id="info-organizador" class="mt-6 bg-indigo-50 border border-indigo-200 rounded-lg p-4">
                        <div class="flex items-start">
                            <svg class="w-5 h-5 text-indigo-600 mt-0.5 mr-3" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            <div>
                                <p class="text-sm font-semibold text-indigo-900">¡Tu primer torneo es GRATIS!</p>
                                <p class="text-xs text-indigo-700 mt-1">Crea tu cuenta y organizá tu primer torneo sin costo alguno.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Términos y Condiciones -->
                    <div class="mt-6">
                        <label class="flex items-start cursor-pointer">
                            <input type="checkbox" id="accept_terms" name="accept_terms" required
                                class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                            <span class="ml-3 text-sm text-gray-700">
                                Acepto los
                                <button type="button" onclick="openModal()" class="text-indigo-600 hover:text-indigo-700 font-semibold underline">
                                    Términos y Condiciones
                                </button> *
                            </span>
                        </label>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" id="submit-btn"
                        class="w-full mt-6 bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-2.5 sm:py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition duration-200 shadow-lg text-sm sm:text-base">
                        Crear Cuenta
                    </button>
                </form>

                <!-- Modal de Términos y Condiciones -->
                <div id="termsModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen px-4 py-8">
                        <div class="bg-white rounded-lg shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
                            <div class="sticky top-0 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-4 flex justify-between items-center">
                                <h2 class="text-xl sm:text-2xl font-bold">Términos y Condiciones</h2>
                                <button type="button" onclick="closeModal()" class="text-white hover:text-gray-200 transition">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="overflow-y-auto max-h-[calc(90vh-140px)] px-6 py-6">
                                <div class="prose prose-sm max-w-none">
                                    <p class="text-gray-600 text-xs mb-4">Última actualización: 13 de octubre de 2025</p>
                                    <p class="text-gray-700 mb-4">Por favor, lea atentamente estos Términos y Condiciones de Uso antes de utilizar la plataforma web <strong>Punto de Oro</strong>.</p>
                                    <p class="text-gray-700 mb-6"><strong>Punto de Oro</strong> es una plataforma web desarrollada para la gestión y seguimiento de torneos deportivos.</p>
                                </div>
                            </div>
                            <div class="sticky bottom-0 bg-gray-50 px-6 py-4 border-t border-gray-200">
                                <button type="button" onclick="closeModal()" class="w-full bg-gradient-to-r from-indigo-600 to-purple-600 text-white py-3 rounded-lg font-semibold hover:from-indigo-700 hover:to-purple-700 transition duration-200">
                                    Cerrar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    const rolFijo = @json($rolFijo);
                    const rolInicial = @json($pending['rol'] !== 'desconocido' ? $pending['rol'] : (old('tipo_registro', 'jugador')));

                    function setTipo(tipo) {
                        if (rolFijo && tipo !== rolInicial) return;

                        document.getElementById('tipo_registro').value = tipo;

                        const btnJugador = document.getElementById('btn-jugador');
                        const btnOrganizador = document.getElementById('btn-organizador');
                        const activeClass = ['bg-white', 'text-indigo-700', 'shadow'];
                        const inactiveClass = ['text-gray-500'];

                        if (tipo === 'jugador') {
                            btnJugador.classList.add(...activeClass);
                            btnJugador.classList.remove(...inactiveClass);
                            btnOrganizador.classList.remove(...activeClass);
                            btnOrganizador.classList.add(...inactiveClass);

                            document.getElementById('field-deporte').classList.remove('hidden');
                            document.getElementById('field-dni').classList.remove('hidden');
                            document.getElementById('field-fecha-nacimiento').classList.remove('hidden');
                            document.getElementById('field-genero').classList.remove('hidden');
                            document.getElementById('field-organizacion').classList.add('hidden');
                            document.getElementById('field-referido').classList.add('hidden');
                            document.getElementById('info-jugador').classList.remove('hidden');
                            document.getElementById('info-organizador').classList.add('hidden');
                            document.getElementById('telefono-req').textContent = '(opcional)';
                            document.getElementById('telefono').removeAttribute('required');
                            document.getElementById('register-title').textContent = 'Registro de Jugador';
                            document.getElementById('register-subtitle').textContent = 'Seguí tus torneos y partidos';
                            document.getElementById('submit-btn').textContent = 'Crear Cuenta de Jugador';
                        } else {
                            btnOrganizador.classList.add(...activeClass);
                            btnOrganizador.classList.remove(...inactiveClass);
                            btnJugador.classList.remove(...activeClass);
                            btnJugador.classList.add(...inactiveClass);

                            document.getElementById('field-deporte').classList.add('hidden');
                            document.getElementById('field-dni').classList.add('hidden');
                            document.getElementById('field-fecha-nacimiento').classList.add('hidden');
                            document.getElementById('field-genero').classList.add('hidden');
                            document.getElementById('field-organizacion').classList.remove('hidden');
                            document.getElementById('field-referido').classList.remove('hidden');
                            document.getElementById('info-jugador').classList.add('hidden');
                            document.getElementById('info-organizador').classList.remove('hidden');
                            document.getElementById('telefono-req').textContent = '*';
                            document.getElementById('telefono').setAttribute('required', 'required');
                            document.getElementById('register-title').textContent = 'Registro de Organizador';
                            document.getElementById('register-subtitle').textContent = 'Crea tu cuenta y gestioná tus torneos';
                            document.getElementById('submit-btn').textContent = 'Crear Cuenta de Organizador';
                        }
                    }

                    document.addEventListener('DOMContentLoaded', function() {
                        setTipo(rolInicial);
                    });

                    function openModal() {
                        document.getElementById('termsModal').classList.remove('hidden');
                        document.body.style.overflow = 'hidden';
                    }

                    function closeModal() {
                        document.getElementById('termsModal').classList.add('hidden');
                        document.body.style.overflow = 'auto';
                    }

                    document.getElementById('termsModal').addEventListener('click', function(e) {
                        if (e.target === this) closeModal();
                    });

                    // Máscara DD/MM/AAAA para fecha de nacimiento
                    document.getElementById('fecha_nacimiento').addEventListener('input', function(e) {
                        let value = e.target.value.replace(/\D/g, '');
                        if (value.length >= 3 && value.length <= 4) {
                            value = value.slice(0, 2) + '/' + value.slice(2);
                        } else if (value.length >= 5) {
                            value = value.slice(0, 2) + '/' + value.slice(2, 4) + '/' + value.slice(4, 8);
                        }
                        e.target.value = value;
                    });
                </script>

                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        ¿Ya tienes cuenta?
                        <a href="{{ route('login') }}" class="text-indigo-600 hover:text-indigo-700 font-semibold">
                            Inicia sesión aquí
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
