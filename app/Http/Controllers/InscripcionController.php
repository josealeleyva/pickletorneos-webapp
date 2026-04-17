<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\InscripcionEquipo;
use App\Models\Jugador;
use App\Models\Torneo;
use App\Services\InscripcionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InscripcionController extends Controller
{
    public function __construct(private InscripcionService $inscripcionService) {}

    public function crear(Torneo $torneo)
    {
        $jugador = Auth::user()->jugador;

        if (! $jugador) {
            return redirect()->route('torneos.public', $torneo->id)
                ->with('error', 'Necesitás tener un perfil de jugador para inscribirte.');
        }

        $categorias = $torneo->categorias()
            ->withPivot(['cupos_categoria', 'numero_grupos', 'tamanio_grupo_id', 'edad_minima', 'edad_maxima', 'genero_permitido'])
            ->get();

        $maxJugadores = $torneo->deporte->getMaxJugadores();
        $requiereNombre = $torneo->deporte->requiereNombreEquipo();
        $elegibilidad = $this->computarElegibilidad($categorias, $jugador);

        return view('inscripciones.crear', compact('torneo', 'categorias', 'jugador', 'maxJugadores', 'requiereNombre', 'elegibilidad'));
    }

    public function store(Request $request, Torneo $torneo)
    {
        $jugador = Auth::user()->jugador;

        if (! $jugador) {
            return redirect()->route('torneos.public', $torneo->id)
                ->with('error', 'Necesitás tener un perfil de jugador para inscribirte.');
        }

        $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'nombre_equipo' => ['nullable', 'string', 'max:100'],
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);

        try {
            $inscripcion = $this->inscripcionService->iniciarInscripcion($jugador, $torneo, $categoria);

            if ($request->filled('nombre_equipo')) {
                $inscripcion->update(['nombre_equipo' => $request->nombre_equipo]);
            }

            return redirect()->route('inscripciones.invitar', $inscripcion)
                ->with('success', '¡Reserva creada! Invitá a tus compañeros (tenés 10 minutos).');
        } catch (\RuntimeException $e) {
            return redirect()->route('torneos.public', $torneo->id)
                ->with('error', $e->getMessage());
        }
    }

    public function buscarJugadores(Request $request, Torneo $torneo)
    {
        $request->validate([
            'categoria_id' => ['required', 'exists:categorias,id'],
            'q' => ['required', 'string', 'min:2'],
        ]);

        $categoria = Categoria::findOrFail($request->categoria_id);

        $jugadores = $this->inscripcionService->buscarJugadoresElegibles(
            $torneo,
            $categoria,
            $request->q
        );

        return response()->json(
            $jugadores->map(fn ($j) => [
                'id' => $j->id,
                'nombre_completo' => $j->nombre_completo,
                'foto' => $j->foto ? asset('storage/'.$j->foto) : null,
            ])
        );
    }

    public function invitar(Request $request, InscripcionEquipo $inscripcion)
    {
        $this->autorizarLider($inscripcion);

        $request->validate([
            'jugador_id' => ['required', 'exists:jugadores,id'],
        ]);

        $jugador = Jugador::findOrFail($request->jugador_id);

        try {
            $this->inscripcionService->enviarInvitacion($inscripcion, $jugador);

            return redirect()->back()->with('success', "Invitación enviada a {$jugador->nombre_completo}.");
        } catch (\RuntimeException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function mostrarInvitaciones(InscripcionEquipo $inscripcion)
    {
        $this->autorizarLider($inscripcion);

        $inscripcion->load(['invitaciones.jugador', 'torneo.deporte', 'categoria']);
        $categorias = $inscripcion->torneo->categorias()->withPivot(['cupos_categoria', 'numero_grupos', 'tamanio_grupo_id', 'edad_minima', 'edad_maxima', 'genero_permitido'])->get();
        $jugador = Auth::user()->jugador;
        $maxJugadores = $inscripcion->torneo->deporte->getMaxJugadores();
        $elegibilidad = $this->computarElegibilidad($categorias, $jugador);

        return view('inscripciones.crear', [
            'torneo' => $inscripcion->torneo,
            'inscripcion' => $inscripcion,
            'categorias' => $categorias,
            'jugador' => $jugador,
            'maxJugadores' => $maxJugadores,
            'requiereNombre' => $inscripcion->torneo->deporte->requiereNombreEquipo(),
            'elegibilidad' => $elegibilidad,
        ]);
    }

    public function cancelar(InscripcionEquipo $inscripcion)
    {
        $this->autorizarLider($inscripcion);

        $this->inscripcionService->cancelarInscripcion($inscripcion, 'jugador');

        return redirect()->route('torneos.public', $inscripcion->torneo_id)
            ->with('success', 'Inscripción cancelada.');
    }

    private function autorizarLider(InscripcionEquipo $inscripcion): void
    {
        $jugador = Auth::user()->jugador;

        if (! $jugador || $inscripcion->lider_jugador_id !== $jugador->id) {
            abort(403);
        }
    }

    /**
     * @param  \Illuminate\Database\Eloquent\Collection<int, \App\Models\Categoria>  $categorias
     * @return array<int, array{elegible: bool, motivo: string|null}>
     */
    private function computarElegibilidad(\Illuminate\Database\Eloquent\Collection $categorias, Jugador $jugador): array
    {
        $elegibilidad = [];

        foreach ($categorias as $cat) {
            $motivo = null;

            $generoPermitido = $cat->pivot->genero_permitido ?? null;

            if ($generoPermitido && $generoPermitido !== 'mixto' && $jugador->genero !== $generoPermitido) {
                $motivo = 'Solo categoría '.ucfirst($generoPermitido);
            }

            $edadMinima = $cat->pivot->edad_minima ?? null;
            $edadMaxima = $cat->pivot->edad_maxima ?? null;

            if (! $motivo && ($edadMinima || $edadMaxima)) {
                if (! $jugador->fecha_nacimiento) {
                    $motivo = 'Completá tu fecha de nacimiento en el perfil';
                } else {
                    $edad = $jugador->fecha_nacimiento->age;

                    if ($edadMinima && $edad < $edadMinima) {
                        $motivo = "Edad mínima: {$edadMinima} años";
                    } elseif ($edadMaxima && $edad > $edadMaxima) {
                        $motivo = "Edad máxima: {$edadMaxima} años";
                    }
                }
            }

            $elegibilidad[$cat->id] = [
                'elegible' => $motivo === null,
                'motivo' => $motivo,
            ];
        }

        return $elegibilidad;
    }
}
