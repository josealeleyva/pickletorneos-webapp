<?php

namespace App\Imports;

use App\Models\Jugador;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\SkipsFailures;

class JugadoresImport implements ToModel, WithHeadingRow, WithMapping, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private int $importedCount = 0;
    private array $skippedDnis = [];

    public function __construct(private int $organizadorId)
    {
    }

    public function map($row): array
    {
        return [
            'nombre'   => $row['nombre'] ?? null,
            'apellido' => $row['apellido'] ?? null,
            'ranking'  => isset($row['ranking'])  && $row['ranking']  !== '' ? (string) $row['ranking']  : null,
            'telefono' => isset($row['telefono']) && $row['telefono'] !== '' ? (string) $row['telefono'] : null,
            'email'    => isset($row['email'])    && $row['email']    !== '' ? (string) $row['email']    : null,
            'dni'      => isset($row['dni'])      && $row['dni']      !== '' ? (string) $row['dni']      : null,
        ];
    }

    public function model(array $row): ?Jugador
    {
        $dni = trim($row['dni'] ?? '');

        // DNI uniqueness check per organizer (can't be expressed as a validation rule without Auth context)
        if (!empty($dni)) {
            $existe = Jugador::where('organizador_id', $this->organizadorId)
                ->where('dni', $dni)
                ->exists();

            if ($existe) {
                $this->skippedDnis[] = $dni;
                return null;
            }
        }

        $this->importedCount++;

        return new Jugador([
            'nombre'         => trim($row['nombre']),
            'apellido'       => trim($row['apellido']),
            'ranking'        => !empty($row['ranking'])  ? trim($row['ranking'])  : null,
            'telefono'       => !empty($row['telefono']) ? trim($row['telefono']) : null,
            'email'          => !empty($row['email'])    ? trim($row['email'])    : null,
            'dni'            => $dni ?: null,
            'organizador_id' => $this->organizadorId,
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre'   => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'email'    => 'nullable|email|max:255',
            'telefono' => 'nullable|string|max:20',
            'ranking'  => 'nullable|string|max:50',
            'dni'      => 'nullable|string|max:20',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'nombre.required'   => 'El nombre es obligatorio (fila :row).',
            'apellido.required' => 'El apellido es obligatorio (fila :row).',
            'email.email'       => 'El email ":input" no tiene un formato válido (fila :row).',
        ];
    }

    public function customValidationAttributes(): array
    {
        return [
            'nombre'   => 'Nombre',
            'apellido' => 'Apellido',
            'email'    => 'Email',
            'telefono' => 'Teléfono',
            'ranking'  => 'Ranking',
            'dni'      => 'DNI',
        ];
    }

    public function getImportedCount(): int
    {
        return $this->importedCount;
    }

    public function getSkippedDnis(): array
    {
        return $this->skippedDnis;
    }
}
