<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Mostrar el perfil del usuario autenticado
     */
    public function show()
    {
        return view('profile.show');
    }

    /**
     * Mostrar formulario para editar perfil
     */
    public function edit()
    {
        return view('profile.edit');
    }

    /**
     * Actualizar el perfil del usuario
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        // Autorizar que el usuario solo puede editar su propio perfil
        $this->authorize('update', $user);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'apellido' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'telefono' => ['required', 'string', 'max:20'],
            'organizacion' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Perfil actualizado exitosamente.');
    }

    /**
     * Mostrar formulario para cambiar contraseña
     */
    public function editPassword()
    {
        return view('profile.change-password');
    }

    /**
     * Actualizar la contraseña del usuario
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        // Autorizar que el usuario solo puede cambiar su propia contraseña
        $this->authorize('update', $user);

        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $user->update([
            'password' => Hash::make($validated['password'])
        ]);

        return redirect()
            ->route('profile.show')
            ->with('success', 'Contraseña actualizada exitosamente.');
    }
}
