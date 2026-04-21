<?php

namespace App\Http\Controllers;

use App\Models\Deporte;
use App\Models\Jugador;
use App\Models\Referido;
use App\Models\User;
use App\Notifications\NuevoReferidoNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Laravel\Socialite\Facades\Socialite;
use Spatie\Permission\Models\Role;

class SocialAuthController extends Controller
{
    /**
     * Redirigir a Google. $rol = 'jugador' | 'organizador' | 'desconocido'
     */
    public function redirectToGoogle(string $rol): RedirectResponse
    {
        session(['google_rol_solicitado' => $rol]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Manejar callback de Google
     */
    public function handleGoogleCallback(): RedirectResponse
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('login')
                ->with('error', 'No se pudo autenticar con Google. Intentá de nuevo.');
        }

        // Buscar usuario existente por google_id o email
        $user = User::where('google_id', $googleUser->getId())
            ->orWhere('email', $googleUser->getEmail())
            ->first();

        if ($user) {
            // Vincular google_id si aún no lo tiene
            if (! $user->google_id) {
                $user->update(['google_id' => $googleUser->getId()]);
            }

            Auth::login($user, true);
            session()->forget(['google_rol_solicitado', 'google_pending']);

            return $this->redirectSegunRol($user);
        }

        // Usuario nuevo: guardar datos en sesión y pedir completar perfil
        $nombreCompleto = $googleUser->getName() ?? '';
        $partes = explode(' ', trim($nombreCompleto), 2);
        $nombre = $partes[0] ?? '';
        $apellido = $partes[1] ?? '';

        session([
            'google_pending' => [
                'google_id' => $googleUser->getId(),
                'email' => $googleUser->getEmail(),
                'nombre' => $nombre,
                'apellido' => $apellido,
                'foto' => $googleUser->getAvatar(),
                'rol' => session('google_rol_solicitado', 'desconocido'),
            ],
        ]);

        session()->forget('google_rol_solicitado');

        return redirect()->route('auth.completar-perfil');
    }

    /**
     * Mostrar formulario para completar datos faltantes
     */
    public function showCompletarPerfil(): \Illuminate\Contracts\View\View|RedirectResponse
    {
        $pending = session('google_pending');

        if (! $pending) {
            return redirect()->route('login')
                ->with('error', 'No hay datos de Google pendientes.');
        }

        return view('auth.completar-perfil', compact('pending'));
    }

    /**
     * Guardar datos y crear el usuario
     */
    public function completarPerfil(Request $request): RedirectResponse
    {
        $pending = session('google_pending');

        if (! $pending) {
            return redirect()->route('login')
                ->with('error', 'Sesión expirada. Intentá autenticarte con Google de nuevo.');
        }

        $rol = $request->input('tipo_registro', $pending['rol']);

        // Validación base (común a ambos roles)
        $rules = [
            'tipo_registro' => 'required|in:jugador,organizador',
            'name' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'accept_terms' => 'accepted',
        ];

        // Reglas según rol
        if ($rol === 'organizador') {
            $rules['telefono'] = 'required|string|max:30';
            $rules['organizacion'] = 'nullable|string|max:255';
            $rules['codigo_referido'] = 'nullable|string|max:10';
        } else {
            $rules['telefono'] = 'nullable|string|max:30';
            $rules['dni'] = 'required|string|max:20';
            $rules['fecha_nacimiento'] = 'required|string';
            $rules['genero'] = 'required|in:masculino,femenino,otro';
        }

        $validated = $request->validate($rules, [
            'accept_terms.accepted' => 'Debes aceptar los Términos y Condiciones.',
            'name.required' => 'El nombre es obligatorio.',
            'apellido.required' => 'El apellido es obligatorio.',
            'telefono.required' => 'El teléfono es obligatorio para organizadores.',
            'dni.required' => 'El DNI es obligatorio para jugadores.',
            'fecha_nacimiento.required' => 'La fecha de nacimiento es obligatoria.',
            'genero.required' => 'El género es obligatorio.',
        ]);

        DB::beginTransaction();
        try {
            // Crear usuario
            $userData = [
                'name' => $validated['name'],
                'apellido' => $validated['apellido'],
                'email' => $pending['email'],
                'google_id' => $pending['google_id'],
                'foto' => $pending['foto'],
                'telefono' => $validated['telefono'] ?? null,
                'password' => null,
                'cuenta_activa' => true,
            ];

            if ($rol === 'organizador') {
                $userData['organizacion'] = $validated['organizacion'] ?? null;
            } else {
                $userData['deporte_principal_id'] = Deporte::where('nombre', 'Pickleball')->first()?->id;
            }

            $user = User::create($userData);

            // Generar código de referido
            $user->generarCodigoReferido();

            // Asignar rol
            $nombreRol = $rol === 'organizador' ? 'Organizador' : 'Jugador';
            $roleModel = Role::where('name', $nombreRol)->where('guard_name', 'web')->first();
            if ($roleModel) {
                $user->assignRole($roleModel);
            }

            // Si es jugador, crear registro en tabla jugadores
            if ($rol === 'jugador') {
                // Parsear fecha DD/MM/AAAA → AAAA-MM-DD
                $fechaNac = null;
                if (! empty($validated['fecha_nacimiento'])) {
                    $partes = explode('/', $validated['fecha_nacimiento']);
                    if (count($partes) === 3) {
                        $fechaNac = $partes[2].'-'.$partes[1].'-'.$partes[0];
                    }
                }

                Jugador::create([
                    'user_id' => $user->id,
                    'organizador_id' => null,
                    'nombre' => $validated['name'],
                    'apellido' => $validated['apellido'],
                    'dni' => $validated['dni'],
                    'fecha_nacimiento' => $fechaNac,
                    'genero' => $validated['genero'],
                    'telefono' => $validated['telefono'] ?? null,
                    'email' => $pending['email'],
                ]);
            }

            // Manejar código de referido (solo organizador)
            if ($rol === 'organizador' && ! empty($validated['codigo_referido'])) {
                $referidor = User::where('codigo_referido', strtoupper($validated['codigo_referido']))->first();
                if ($referidor && $referidor->id !== $user->id) {
                    $user->update(['referido_por_id' => $referidor->id]);
                    Referido::create([
                        'referidor_id' => $referidor->id,
                        'referido_id' => $user->id,
                        'fecha_registro' => now(),
                        'estado' => 'pendiente',
                    ]);
                    // Notificar al referidor
                    try {
                        $referidor->notify(new NuevoReferidoNotification($user));
                    } catch (\Exception $e) {
                        // No bloquear el registro si falla la notificación
                    }
                }
            }

            DB::commit();

            Auth::login($user, true);
            session()->forget('google_pending');

            return $this->redirectSegunRol($user);

        } catch (\Exception $e) {
            DB::rollBack();

            \Illuminate\Support\Facades\Log::error('Google OAuth completarPerfil error: '.$e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Error al crear la cuenta. Por favor intentá de nuevo.')->withInput();
        }
    }

    /**
     * Redirigir al dashboard según el rol del usuario
     */
    private function redirectSegunRol(User $user): RedirectResponse
    {
        if ($user->hasRole('Superadministrador')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('Organizador')) {
            return redirect()->route('dashboard');
        }

        if ($user->hasRole('Jugador')) {
            return redirect()->route('jugador.torneos');
        }

        return redirect()->route('dashboard');
    }
}
