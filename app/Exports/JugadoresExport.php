<?php

namespace App\Exports;

use App\Models\Jugador;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JugadoresExport implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function __construct(private int $organizadorId)
    {
    }

    public function collection(): Collection
    {
        return Jugador::where('organizador_id', $this->organizadorId)
            ->orderBy('apellido')
            ->orderBy('nombre')
            ->get(['nombre', 'apellido', 'ranking', 'telefono', 'email', 'dni']);
    }

    public function headings(): array
    {
        return ['Nombre', 'Apellido', 'Ranking', 'Teléfono', 'Email', 'DNI'];
    }

    public function title(): string
    {
        return 'Jugadores';
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['rgb' => '4F46E5'],
                ],
            ],
        ];
    }
}
