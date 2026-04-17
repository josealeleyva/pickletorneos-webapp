@extends('layouts.jugador')

@section('title', 'Mi Perfil')
@section('page-title', 'Mi Perfil')

@section('content')
@php use Illuminate\Support\Facades\Storage; @endphp
<div class="max-w-2xl mx-auto space-y-4">

    {{-- Mensajes de éxito --}}
    @if(session('success_perfil'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800 font-medium">
            {{ session('success_perfil') }}
        </div>
    @endif
    @if(session('success_foto'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800 font-medium">
            {{ session('success_foto') }}
        </div>
    @endif
    @if(session('success_password'))
        <div class="bg-green-50 border border-green-200 rounded-lg p-4 text-sm text-green-800 font-medium">
            {{ session('success_password') }}
        </div>
    @endif

    {{-- Card 1: Header con foto --}}
    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
        <div class="bg-gradient-to-r from-brand-700 to-brand-500 px-4 py-6">
            <div class="flex flex-col items-center gap-3">

                {{-- Formulario de foto (submit automático al seleccionar) --}}
                <form action="{{ route('jugador.perfil.foto') }}" method="POST"
                      enctype="multipart/form-data" id="form-foto">
                    @csrf
                    <div class="relative">
                        @if($jugador?->foto)
                            <img src="{{ asset('storage/' . $jugador->foto) }}"
                                 alt="Foto de perfil"
                                 class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg">
                        @else
                            <div class="w-24 h-24 rounded-full bg-white flex items-center justify-center border-4 border-white shadow-lg">
                                <span class="text-3xl font-bold text-brand-600">
                                    {{ substr(auth()->user()->name, 0, 1) }}{{ substr(auth()->user()->apellido ?? '', 0, 1) }}
                                </span>
                            </div>
                        @endif
                        <label for="foto-input"
                               class="absolute bottom-0 right-0 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-md cursor-pointer hover:bg-brand-50 transition"
                               title="Cambiar foto">
                            <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </label>
                        <input type="file" id="foto-input" name="foto" accept="image/*"
                               class="hidden"
                               onchange="document.getElementById('form-foto').submit()">
                    </div>
                    @if($errors->foto->has('foto'))
                        <p class="mt-2 text-xs text-red-200 text-center">{{ $errors->foto->first('foto') }}</p>
                    @endif
                </form>

                <div class="text-center">
                    <h2 class="text-xl font-bold text-white">
                        {{ auth()->user()->name }} {{ auth()->user()->apellido }}
                    </h2>
                    <p class="text-brand-100 text-sm mt-0.5">{{ auth()->user()->email }}</p>
                    @if(auth()->user()->deportePrincipal)
                        <span class="mt-2 inline-block px-3 py-1 bg-white bg-opacity-20 text-white rounded-full text-xs font-medium">
                            {{ auth()->user()->deportePrincipal->nombre }}
                        </span>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Card 2: Datos personales --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 sm:p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Datos personales</h3>

        <form action="{{ route('jugador.perfil.update') }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

                {{-- Nombre --}}
                <div>
                    <label for="name" class="block text-xs font-medium text-gray-500 mb-1">
                        Nombre <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name"
                           value="{{ old('name', auth()->user()->name) }}"
                           required
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->perfil->has('name') ? 'border-red-500' : 'border-gray-300' }}">
                    @if($errors->perfil->has('name'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->perfil->first('name') }}</p>
                    @endif
                </div>

                {{-- Apellido --}}
                <div>
                    <label for="apellido" class="block text-xs font-medium text-gray-500 mb-1">
                        Apellido <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="apellido" name="apellido"
                           value="{{ old('apellido', auth()->user()->apellido) }}"
                           required
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->perfil->has('apellido') ? 'border-red-500' : 'border-gray-300' }}">
                    @if($errors->perfil->has('apellido'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->perfil->first('apellido') }}</p>
                    @endif
                </div>

                {{-- Email --}}
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-500 mb-1">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', auth()->user()->email) }}"
                           required
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->perfil->has('email') ? 'border-red-500' : 'border-gray-300' }}">
                    @if($errors->perfil->has('email'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->perfil->first('email') }}</p>
                    @endif
                </div>

                {{-- Teléfono --}}
                <div>
                    <label for="telefono" class="block text-xs font-medium text-gray-500 mb-1">Teléfono</label>
                    <input type="text" id="telefono" name="telefono"
                           value="{{ old('telefono', auth()->user()->telefono) }}"
                           placeholder="3416123456"
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->perfil->has('telefono') ? 'border-red-500' : 'border-gray-300' }}">
                    @if($errors->perfil->has('telefono'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->perfil->first('telefono') }}</p>
                    @endif
                </div>

                {{-- DNI --}}
                <div>
                    <label for="dni" class="block text-xs font-medium text-gray-500 mb-1">DNI / CI</label>
                    <input type="text" id="dni" name="dni"
                           value="{{ old('dni', $jugador?->dni) }}"
                           placeholder="12345678"
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->perfil->has('dni') ? 'border-red-500' : 'border-gray-300' }}">
                    @if($errors->perfil->has('dni'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->perfil->first('dni') }}</p>
                    @endif
                </div>

                {{-- Fecha de nacimiento --}}
                <div>
                    <label for="fecha_nacimiento" class="block text-xs font-medium text-gray-500 mb-1">Fecha de nacimiento</label>
                    <input type="text" id="fecha_nacimiento" name="fecha_nacimiento"
                           value="{{ old('fecha_nacimiento', $jugador?->fecha_nacimiento?->format('d/m/Y')) }}"
                           placeholder="DD/MM/AAAA"
                           maxlength="10"
                           autocomplete="off"
                           class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->perfil->has('fecha_nacimiento') ? 'border-red-500' : 'border-gray-300' }}">
                    @if($errors->perfil->has('fecha_nacimiento'))
                        <p class="mt-1 text-xs text-red-600">{{ $errors->perfil->first('fecha_nacimiento') }}</p>
                    @endif
                </div>

                {{-- Género --}}
                <div>
                    <label for="genero" class="block text-xs font-medium text-gray-500 mb-1">Género</label>
                    <select id="genero" name="genero"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent bg-white">
                        <option value="">No especificado</option>
                        <option value="masculino" @selected(old('genero', $jugador?->genero) === 'masculino')>Masculino</option>
                        <option value="femenino"  @selected(old('genero', $jugador?->genero) === 'femenino')>Femenino</option>
                        <option value="otro"      @selected(old('genero', $jugador?->genero) === 'otro')>Otro</option>
                    </select>
                </div>

                {{-- Deporte principal --}}
                <div>
                    <label for="deporte_principal_id" class="block text-xs font-medium text-gray-500 mb-1">Deporte principal</label>
                    <select id="deporte_principal_id" name="deporte_principal_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent bg-white">
                        <option value="">Sin especificar</option>
                        @foreach($deportes as $deporte)
                            <option value="{{ $deporte->id }}"
                                    @selected(old('deporte_principal_id', auth()->user()->deporte_principal_id) == $deporte->id)>
                                {{ $deporte->nombre }}
                            </option>
                        @endforeach
                    </select>
                </div>

            </div>

            {{-- Toggle auto-aceptar invitaciones --}}
            <div class="flex items-center justify-between py-3 border-t border-gray-100">
                <div>
                    <p class="text-sm font-medium text-gray-900">Auto-aceptar invitaciones</p>
                    <p class="text-xs text-gray-500 mt-0.5">Aceptar automáticamente invitaciones de jugadores con quienes ya jugaste.</p>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="auto_aceptar_invitaciones" value="1"
                           {{ $jugador?->auto_aceptar_invitaciones ? 'checked' : '' }}
                           class="sr-only peer">
                    <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-brand-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-brand-600"></div>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100">
                <button type="submit"
                        class="w-full sm:w-auto px-6 py-2 bg-brand-600 hover:bg-brand-700 text-white font-semibold rounded-lg shadow-sm transition text-sm">
                    Guardar cambios
                </button>
            </div>
        </form>
    </div>

    {{-- Card 3: Seguridad --}}
    <div class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 sm:p-6">
        <h3 class="text-base font-semibold text-gray-800 mb-4">Seguridad</h3>

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
            <form action="{{ route('jugador.perfil.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-4 mb-4">
                    <div>
                        <label for="current_password" class="block text-xs font-medium text-gray-500 mb-1">
                            Contraseña actual <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="current_password" name="current_password"
                               required
                               class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->password->has('current_password') ? 'border-red-500' : 'border-gray-300' }}">
                        @if($errors->password->has('current_password'))
                            <p class="mt-1 text-xs text-red-600">{{ $errors->password->first('current_password') }}</p>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-xs font-medium text-gray-500 mb-1">
                                Nueva contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password"
                                   required
                                   placeholder="Mínimo 8 caracteres"
                                   class="w-full px-3 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent {{ $errors->password->has('password') ? 'border-red-500' : 'border-gray-300' }}">
                            @if($errors->password->has('password'))
                                <p class="mt-1 text-xs text-red-600">{{ $errors->password->first('password') }}</p>
                            @endif
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-xs font-medium text-gray-500 mb-1">
                                Confirmar contraseña <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation"
                                   required
                                   placeholder="Repetí la nueva contraseña"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                        </div>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="w-full sm:w-auto px-6 py-2 bg-gray-800 hover:bg-gray-900 text-white font-semibold rounded-lg shadow-sm transition text-sm">
                        Cambiar contraseña
                    </button>
                </div>
            </form>
        @endif
    </div>

</div>

@push('scripts')
<script>
    document.getElementById('fecha_nacimiento')?.addEventListener('input', function (e) {
        let v = e.target.value.replace(/\D/g, '').substring(0, 8);
        if (v.length >= 5) {
            v = v.substring(0, 2) + '/' + v.substring(2, 4) + '/' + v.substring(4);
        } else if (v.length >= 3) {
            v = v.substring(0, 2) + '/' + v.substring(2);
        }
        e.target.value = v;
    });
</script>
@endpush
@endsection
