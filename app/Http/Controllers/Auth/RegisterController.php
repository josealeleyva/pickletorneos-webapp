<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Roles;
use App\Http\Controllers\Controller;
use App\Models\Deporte;
use App\Models\Jugador;
use App\Models\Referido;
use App\Models\User;
use App\Notifications\NuevoReferidoNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisterController extends Controller
{
    /**
     * Mostrar el formulario de registro
     */
    public function create()
    {
        return view('auth.register');
    }

    /**
     * Procesar el registro de un nuevo usuario (jugador u organizador)
     */
    public function store(Request $request)
    {
        $tipo = $request->input('tipo_registro', 'organizador');

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'g-recaptcha-response' => app()->isLocal() ? [] : ['required', 'captcha'],
        ];

        if ($tipo === 'jugador') {
            $rules['telefono'] = ['nullable', 'string', 'max:20'];
            $rules['dni'] = ['required', 'string', 'max:20'];
            $rules['fecha_nacimiento'] = ['required', 'date_format:d/m/Y'];
            $rules['genero'] = ['required', 'in:masculino,femenino,otro'];
        } else {
            $rules['telefono'] = ['required', 'string', 'max:20'];
            $rules['organizacion'] = ['nullable', 'string', 'max:255'];
            $rules['codigo_referido'] = ['nullable', 'string', 'max:10'];
        }

        $request->validate($rules);

        if ($tipo === 'jugador') {
            return $this->registrarJugador($request);
        }

        return $this->registrarOrganizador($request);
    }

    private function registrarJugador(Request $request)
    {
        $pickleball = Deporte::where('nombre', 'Pickleball')->first();

        $user = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'deporte_principal_id' => $pickleball?->id,
            'password' => Hash::make($request->password),
            'cuenta_activa' => true,
        ]);

        $user->assignRole(Roles::Jugador->value);

        // Crear registro en tabla jugadores vinculado al usuario
        Jugador::create([
            'nombre' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'dni' => $request->dni,
            'fecha_nacimiento' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->fecha_nacimiento)->format('Y-m-d'),
            'genero' => $request->genero,
            'user_id' => $user->id,
        ]);

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('jugador.dashboard')->with('success', '¡Bienvenido a PickleTorneos! Ya podés seguir tus partidos y torneos.');
    }

    private function registrarOrganizador(Request $request)
    {
        $user = User::create([
            'name' => $request->name,
            'apellido' => $request->apellido,
            'email' => $request->email,
            'telefono' => $request->telefono,
            'organizacion' => $request->organizacion,
            'password' => Hash::make($request->password),
            'cuenta_activa' => true,
            'torneos_creados' => 0,
        ]);

        $user->generarCodigoReferido();
        $user->assignRole(Roles::Organizador->value);

        if (! empty($request->codigo_referido)) {
            $referidor = User::where('codigo_referido', strtoupper($request->codigo_referido))->first();

            if ($referidor) {
                $user->update(['referido_por_id' => $referidor->id]);

                Referido::create([
                    'referidor_id' => $referidor->id,
                    'referido_id' => $user->id,
                    'fecha_registro' => now(),
                    'estado' => 'pendiente',
                ]);

                $referidor->notify(new NuevoReferidoNotification($user));
            }
        }

        event(new Registered($user));
        Auth::login($user);

        return redirect()->route('dashboard')->with('success', '¡Cuenta creada exitosamente! Tu primer torneo es GRATIS 🎉');
    }

    /**
     * Validar código de referido (AJAX)
     */
    public function validarCodigo(Request $request)
    {
        $request->validate([
            'codigo' => 'required|string|max:10',
        ]);

        $referidor = User::where('codigo_referido', strtoupper($request->codigo))->first();

        if (! $referidor) {
            return response()->json([
                'valido' => false,
                'mensaje' => 'Código de referido inválido',
            ]);
        }

        return response()->json([
            'valido' => true,
            'referidor' => [
                'nombre' => $referidor->name.' '.$referidor->apellido,
                'organizacion' => $referidor->organizacion,
            ],
            'beneficio' => '20% de descuento en tu primer torneo pago',
        ]);
    }
}
