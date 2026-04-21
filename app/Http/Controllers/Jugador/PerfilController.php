<?php

namespace App\Http\Controllers\Jugador;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class PerfilController extends Controller
{
    public function show(): View
    {
        $jugador = auth()->user()->jugador;

        return view('jugador.perfil', compact('jugador'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = auth()->user();
        $jugador = $user->jugador;

        $validated = $request->validateWithBag('perfil', [
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'telefono' => ['nullable', 'string', 'max:20'],
            'dni' => ['nullable', 'string', 'max:20'],
            'fecha_nacimiento' => ['nullable', 'date_format:d/m/Y'],
            'genero' => ['nullable', 'in:masculino,femenino,otro'],
            'auto_aceptar_invitaciones' => ['boolean'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'apellido' => $validated['apellido'],
            'email' => $validated['email'],
            'telefono' => $validated['telefono'] ?? null,
        ]);

        if ($jugador) {
            $jugador->update([
                'nombre' => $validated['name'],
                'apellido' => $validated['apellido'],
                'email' => $validated['email'],
                'telefono' => $validated['telefono'] ?? null,
                'dni' => $validated['dni'] ?? null,
                'fecha_nacimiento' => isset($validated['fecha_nacimiento'])
                    ? \Carbon\Carbon::createFromFormat('d/m/Y', $validated['fecha_nacimiento'])->format('Y-m-d')
                    : null,
                'genero' => $validated['genero'] ?? null,
                'auto_aceptar_invitaciones' => $request->boolean('auto_aceptar_invitaciones'),
            ]);
        }

        return redirect()->route('jugador.perfil')->with('success_perfil', 'Datos actualizados correctamente.');
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $request->validateWithBag('password', [
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('jugador.perfil')->with('success_password', 'Contraseña cambiada exitosamente.');
    }

    public function updateFoto(Request $request): RedirectResponse
    {
        $request->validateWithBag('foto', [
            'foto' => ['required', 'image', 'max:2048'],
        ]);

        $jugador = auth()->user()->jugador;

        if (! $jugador) {
            return redirect()->route('jugador.perfil')->with('success_foto', 'No se encontró perfil de jugador.');
        }

        if ($jugador->foto) {
            Storage::disk('public')->delete($jugador->foto);
        }

        $path = $request->file('foto')->store('jugadores/fotos', 'public');
        $jugador->update(['foto' => $path]);

        return redirect()->route('jugador.perfil')->with('success_foto', 'Foto actualizada correctamente.');
    }
}
