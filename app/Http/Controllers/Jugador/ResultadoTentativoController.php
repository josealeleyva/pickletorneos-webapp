<?php

namespace App\Http\Controllers\Jugador;

use App\Http\Controllers\Controller;
use App\Models\Notificacion;
use App\Models\Partido;
use App\Models\ResultadoTentativo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ResultadoTentativoController extends Controller
{
    public function store(Request $request, Partido $partido): \Illuminate\Http\RedirectResponse
    {
        $jugador = auth()->user()->jugador;

        if (! $jugador) {
            return back()->withErrors(['error' => 'No tenés perfil de jugador.']);
        }

        $equipoIds = $jugador->equipos()->pluck('equipos.id');
        $esEquipo1 = $equipoIds->contains($partido->equipo1_id);
        $esEquipo2 = $equipoIds->contains($partido->equipo2_id);

        if (! $esEquipo1 && ! $esEquipo2) {
            return back()->withErrors(['error' => 'No pertenecés a ninguno de los equipos de este partido.']);
        }

        if (! $partido->fecha_hora || $partido->fecha_hora->isFuture()) {
            return back()->withErrors(['error' => 'El partido todavía no comenzó.']);
        }

        if ($partido->equipo_ganador_id) {
            return back()->withErrors(['error' => 'Este partido ya tiene resultado oficial.']);
        }

        if (ResultadoTentativo::where('partido_id', $partido->id)->exists()) {
            return back()->withErrors(['error' => 'Ya existe un resultado propuesto para este partido. Esperá a que el rival lo confirme o lo modifique.']);
        }

        $rivalEquipoTemp = $esEquipo1 ? $partido->equipo2 : $partido->equipo1;
        $rivalTieneUsuarios = $rivalEquipoTemp->jugadores()->whereNotNull('user_id')->exists();

        if (! $rivalTieneUsuarios) {
            return back()->withErrors(['error' => 'El equipo rival no tiene jugadores registrados en la plataforma. El organizador del torneo debe cargar el resultado manualmente.']);
        }

        $validated = $request->validate([
            'juegos' => 'required|array|min:1',
            'juegos.*.juego_equipo1' => 'required|integer|min:0',
            'juegos.*.juego_equipo2' => 'required|integer|min:0',
        ]);

        $totalEquipo1 = 0;
        $totalEquipo2 = 0;
        foreach ($validated['juegos'] as $juego) {
            $totalEquipo1 += $juego['juego_equipo1'];
            $totalEquipo2 += $juego['juego_equipo2'];
        }

        $ganadorId = null;
        if ($totalEquipo1 > $totalEquipo2) {
            $ganadorId = $partido->equipo1_id;
        } elseif ($totalEquipo2 > $totalEquipo1) {
            $ganadorId = $partido->equipo2_id;
        }

        $miEquipo = $esEquipo1 ? $partido->equipo1 : $partido->equipo2;
        $rivalEquipo = $esEquipo1 ? $partido->equipo2 : $partido->equipo1;

        ResultadoTentativo::create([
            'partido_id' => $partido->id,
            'propuesto_por_equipo_id' => $miEquipo->id,
            'propuesto_por_jugador_id' => $jugador->id,
            'juegos' => $validated['juegos'],
            'sets_equipo1' => $totalEquipo1,
            'sets_equipo2' => $totalEquipo2,
            'equipo_ganador_id' => $ganadorId,
        ]);

        $torneoId = $partido->equipo1->torneo_id;
        $mensaje = "{$miEquipo->nombre} propuso el resultado del partido vs {$rivalEquipo->nombre}. Confirmá o modificá.";
        $this->notificarEquipo($rivalEquipo, $torneoId, $mensaje, 'resultado_tentativo');

        return redirect()->route('jugador.partidos')->with('success', 'Resultado propuesto. Esperando confirmación del equipo rival.');
    }

    public function confirmar(ResultadoTentativo $resultado): \Illuminate\Http\RedirectResponse
    {
        $jugador = auth()->user()->jugador;

        if (! $jugador) {
            abort(403);
        }

        $partido = $resultado->partido;
        $equipoIds = $jugador->equipos()->pluck('equipos.id');

        // Debe pertenecer al equipo rival (no al que propuso)
        $equiposDelPartido = collect([$partido->equipo1_id, $partido->equipo2_id]);
        $misEquiposEnPartido = $equipoIds->intersect($equiposDelPartido);

        if ($misEquiposEnPartido->isEmpty()) {
            abort(403, 'No pertenecés a ningún equipo de este partido.');
        }

        if ($misEquiposEnPartido->contains($resultado->propuesto_por_equipo_id)) {
            abort(403, 'No podés confirmar tu propio resultado.');
        }

        DB::transaction(function () use ($resultado, $partido) {
            $partido->update([
                'sets_equipo1' => $resultado->sets_equipo1,
                'sets_equipo2' => $resultado->sets_equipo2,
                'equipo_ganador_id' => $resultado->equipo_ganador_id,
                'estado' => 'finalizado',
            ]);

            foreach ($resultado->juegos as $index => $juego) {
                \App\Models\Juego::create([
                    'partido_id' => $partido->id,
                    'numero_juego' => $index + 1,
                    'juegos_equipo1' => $juego['juego_equipo1'],
                    'juegos_equipo2' => $juego['juego_equipo2'],
                    'tipo_juego' => 'partido',
                ]);
            }

            $resultado->delete();
        });

        // Notificar al equipo proponente original
        $partido->loadMissing(['equipo1', 'equipo2']);
        $torneoId = $partido->equipo1->torneo_id;

        $equipoProponente = $partido->equipo1_id === $resultado->propuesto_por_equipo_id
            ? $partido->equipo1
            : $partido->equipo2;
        $equipoConfirmador = $partido->equipo1_id === $resultado->propuesto_por_equipo_id
            ? $partido->equipo2
            : $partido->equipo1;

        $mensaje = "{$equipoConfirmador->nombre} confirmó el resultado del partido vs {$equipoProponente->nombre}.";
        $this->notificarEquipo($equipoProponente, $torneoId, $mensaje, 'resultado_confirmado');

        // Intentar finalizar torneo automáticamente
        $torneo = $partido->equipo1->torneo;
        if ($torneo) {
            \App\Http\Controllers\TorneoController::intentarFinalizarAutomatico($torneo);
        }

        return redirect()->route('jugador.partidos')->with('success', 'Resultado confirmado. El partido ha finalizado.');
    }

    public function modificar(Request $request, ResultadoTentativo $resultado): \Illuminate\Http\RedirectResponse
    {
        $jugador = auth()->user()->jugador;

        if (! $jugador) {
            abort(403);
        }

        $partido = $resultado->partido;
        $equipoIds = $jugador->equipos()->pluck('equipos.id');

        $equiposDelPartido = collect([$partido->equipo1_id, $partido->equipo2_id]);
        $misEquiposEnPartido = $equipoIds->intersect($equiposDelPartido);

        if ($misEquiposEnPartido->isEmpty()) {
            abort(403, 'No pertenecés a ningún equipo de este partido.');
        }

        if ($misEquiposEnPartido->contains($resultado->propuesto_por_equipo_id)) {
            abort(403, 'No podés modificar tu propio resultado.');
        }

        $validated = $request->validate([
            'juegos' => 'required|array|min:1',
            'juegos.*.juego_equipo1' => 'required|integer|min:0',
            'juegos.*.juego_equipo2' => 'required|integer|min:0',
        ]);

        $totalEquipo1 = 0;
        $totalEquipo2 = 0;
        foreach ($validated['juegos'] as $juego) {
            $totalEquipo1 += $juego['juego_equipo1'];
            $totalEquipo2 += $juego['juego_equipo2'];
        }

        $ganadorId = null;
        if ($totalEquipo1 > $totalEquipo2) {
            $ganadorId = $partido->equipo1_id;
        } elseif ($totalEquipo2 > $totalEquipo1) {
            $ganadorId = $partido->equipo2_id;
        }

        $miEquipo = $misEquiposEnPartido->contains($partido->equipo1_id) ? $partido->equipo1 : $partido->equipo2;

        // Guardar quién propuso originalmente para notificar
        $equipoOriginal = $partido->equipo1_id === $resultado->propuesto_por_equipo_id
            ? $partido->equipo1
            : $partido->equipo2;

        $resultado->update([
            'propuesto_por_equipo_id' => $miEquipo->id,
            'propuesto_por_jugador_id' => $jugador->id,
            'juegos' => $validated['juegos'],
            'sets_equipo1' => $totalEquipo1,
            'sets_equipo2' => $totalEquipo2,
            'equipo_ganador_id' => $ganadorId,
        ]);

        $torneoId = $partido->equipo1->torneo_id;
        $mensaje = "{$miEquipo->nombre} modificó el resultado propuesto. Revisá y confirmá.";
        $this->notificarEquipo($equipoOriginal, $torneoId, $mensaje, 'resultado_tentativo');

        return redirect()->route('jugador.partidos')->with('success', 'Resultado modificado. Esperando confirmación del equipo rival.');
    }

    private function notificarEquipo(\App\Models\Equipo $equipo, int $torneoId, string $mensaje, string $tipo): void
    {
        $equipo->jugadores()->whereNotNull('user_id')->get()
            ->each(function ($jugador) use ($torneoId, $mensaje, $tipo) {
                $notificacion = Notificacion::create([
                    'torneo_id' => $torneoId,
                    'mensaje' => $mensaje,
                    'tipo' => $tipo,
                    'enviado_at' => Carbon::now(),
                ]);
                $notificacion->usuarios()->attach($jugador->user_id, ['leida' => false]);
            });
    }
}
