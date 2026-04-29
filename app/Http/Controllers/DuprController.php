<?php

namespace App\Http\Controllers;

use App\Services\DuprService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DuprController extends Controller
{
    public function __construct(private DuprService $duprService) {}

    public function buscar(Request $request): JsonResponse
    {
        $query = $request->string('q')->toString();

        if (strlen($query) < 2) {
            return response()->json(['hits' => []]);
        }

        $hits = $this->duprService->buscarJugadores($query);

        return response()->json(['hits' => $hits]);
    }

    public function vincular(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'dupr_id' => ['required', 'string', 'max:10'],
            'dupr_nombre' => ['required', 'string', 'max:255'],
        ]);

        $jugador = auth()->user()->jugador;

        if (! $jugador) {
            return redirect()->route('jugador.perfil')->with('error_dupr', 'No se encontró perfil de jugador.');
        }

        $ratings = $this->duprService->obtenerRatingJugador($validated['dupr_id']);

        $jugador->update([
            'dupr_id' => $validated['dupr_id'],
            'rating_singles' => $ratings['singles'] ?? null,
            'rating_doubles' => $ratings['doubles'] ?? null,
            'dupr_sincronizado_at' => now(),
        ]);

        return redirect()->route('jugador.perfil')->with('success_dupr', 'Cuenta DUPR vinculada correctamente.');
    }

    public function autoconectar(): JsonResponse
    {
        $user = auth()->user();

        if (! $user->jugador) {
            return response()->json(['encontrado' => false]);
        }

        $nombreCompleto = trim("{$user->name} {$user->apellido}");
        $hits = $this->duprService->buscarJugadores($nombreCompleto, 5);

        if (empty($hits)) {
            return response()->json(['encontrado' => false]);
        }

        $jugadores = array_map(fn ($h) => [
            'duprId' => $h['duprId'],
            'fullName' => $h['fullName'],
            'rating_singles' => $h['ratings']['singles']['rating'] ?? null,
            'rating_doubles' => $h['ratings']['doubles']['rating'] ?? null,
        ], $hits);

        return response()->json(['encontrado' => true, 'jugadores' => $jugadores]);
    }

    public function crear(): JsonResponse
    {
        $user = auth()->user();
        $jugador = $user->jugador;

        if (! $jugador) {
            return response()->json(['vinculado' => false, 'mensaje' => 'No se encontró perfil de jugador.']);
        }

        $nombreCompleto = trim("{$user->name} {$user->apellido}");
        $duprId = $this->duprService->invitarJugador($nombreCompleto, $user->email);

        if (! $duprId) {
            return response()->json([
                'vinculado' => false,
                'mensaje' => 'No pudimos crear tu cuenta automáticamente. Registrate en dupr.gg',
                'redirect' => 'https://dupr.gg/signup',
            ]);
        }

        $ratings = $this->duprService->obtenerRatingJugador($duprId);

        $jugador->update([
            'dupr_id' => $duprId,
            'rating_singles' => $ratings['singles'] ?? null,
            'rating_doubles' => $ratings['doubles'] ?? null,
            'dupr_sincronizado_at' => now(),
        ]);

        return response()->json(['vinculado' => true]);
    }

    public function desconectar(): RedirectResponse
    {
        $jugador = auth()->user()->jugador;

        if ($jugador) {
            $jugador->update([
                'dupr_id' => null,
                'rating_singles' => null,
                'rating_doubles' => null,
                'dupr_sincronizado_at' => null,
            ]);
        }

        return redirect()->route('jugador.perfil')->with('success_dupr', 'Cuenta DUPR desvinculada.');
    }
}
