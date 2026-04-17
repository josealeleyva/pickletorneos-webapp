<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PlantillaJugadoresExport implements FromArray, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    public function array(): array
    {
        return [
            ['Juan', 'Pérez', '1500', '3415001234', 'juan.perez@ejemplo.com', '30123456'],
        ];
    }

    public function headings(): array
    {
        return ['Nombre *', 'Apellido *', 'Ranking', 'Teléfono', 'Email', 'DNI'];
    }

    public function title(): string
    {
        return 'Plantilla Jugadores';
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
            2 => [
                'fill' => [
                    'fillType'   => 'solid',
                    'startColor' => ['rgb' => 'F3F4F6'],
                ],
            ],
        ];
    }
}
