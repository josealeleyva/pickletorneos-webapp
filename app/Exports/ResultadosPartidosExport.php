<?php

namespace App\Exports;

use App\Models\Torneo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class ResultadosPartidosExport implements FromCollection, WithHeadings, WithTitle
{
    public function __construct(private Torneo $torneo)
    {
    }

    public function collection()
    {
        $esFutbol = $this->torneo->deporte->esFutbol();

        $partidos = $this->torneo->partidos()
            ->where('estado', 'finalizado')
            ->with(['equipo1', 'equipo2', 'equipoGanador', 'cancha', 'grupo.categoria', 'llave.categoria', 'juegos' => function ($q) {
                $q->orderBy('numero_juego');
            }])
            ->orderBy('fecha_hora')
            ->get();

        return $partidos->map(function ($partido) use ($esFutbol) {
            // Tipo de partido
            $tipo = $partido->grupo_id ? 'Grupo' : 'Llave';

            // Categoría
            $categoria = '-';
            if ($partido->grupo?->categoria) {
                $categoria = $partido->grupo->categoria->nombre;
            } elseif ($partido->llave?->categoria) {
                $categoria = $partido->llave->categoria->nombre;
            }

            // Grupo o ronda
            $grupoRonda = '-';
            if ($partido->grupo) {
                $grupoRonda = $partido->grupo->nombre ?? 'Grupo';
            } elseif ($partido->llave) {
                $grupoRonda = $partido->llave->ronda ?? '-';
            }

            // Detalle de juegos (ej: "21-15, 21-18")
            $detalle = $partido->juegos->map(function ($juego) {
                return "{$juego->juegos_equipo1}-{$juego->juegos_equipo2}";
            })->implode(', ');

            // Ganador
            $ganador = $partido->equipoGanador?->nombre ?? 'Empate';

            return [
                'tipo'        => $tipo,
                'categoria'   => $categoria,
                'grupo_ronda' => $grupoRonda,
                'equipo1'     => $partido->equipo1?->nombre ?? '-',
                'equipo2'     => $partido->equipo2?->nombre ?? '-',
                'score_e1'    => $partido->sets_equipo1,
                'score_e2'    => $partido->sets_equipo2,
                'detalle'     => $detalle,
                'ganador'     => $ganador,
                'fecha'       => $partido->fecha_hora?->format('d/m/Y H:i') ?? '-',
                'cancha'      => $partido->cancha?->nombre ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        $esFutbol = $this->torneo->deporte->esFutbol();
        $labelScore = $esFutbol ? 'Goles' : 'Sets';

        return [
            'Tipo',
            'Categoría',
            'Grupo / Ronda',
            'Equipo 1',
            'Equipo 2',
            "{$labelScore} E1",
            "{$labelScore} E2",
            $esFutbol ? 'Detalle Goles' : 'Detalle Games',
            'Ganador',
            'Fecha / Hora',
            'Cancha',
        ];
    }

    public function title(): string
    {
        return 'Resultados';
    }
}
