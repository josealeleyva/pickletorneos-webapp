<?php

namespace App\Enums;

enum TipoFormatoTorneo: string
{
    case ELIMINACION_DIRECTA = 'Eliminación Directa';
    case FASE_GRUPOS_ELIMINACION = 'Fase de Grupos + Eliminación';
    case LIGA = 'Liga';

    /**
     * Determinar si el formato requiere grupos
     */
    public function tieneGrupos(): bool
    {
        return match($this) {
            self::FASE_GRUPOS_ELIMINACION => true,
            self::ELIMINACION_DIRECTA, self::LIGA => false,
        };
    }

    /**
     * Obtener el orden para mostrar en UI
     */
    public function getOrden(): int
    {
        return match($this) {
            self::ELIMINACION_DIRECTA => 1,
            self::FASE_GRUPOS_ELIMINACION => 2,
            self::LIGA => 3,
        };
    }

    /**
     * Obtener todos los valores de formatos
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Obtener el valor string del enum
     */
    public function value(): string
    {
        return $this->value;
    }

    /**
     * Verificar si es eliminación directa
     */
    public function esEliminacionDirecta(): bool
    {
        return $this === self::ELIMINACION_DIRECTA;
    }

    /**
     * Verificar si es liga (anteriormente Round Robin)
     */
    public function esLiga(): bool
    {
        return $this === self::LIGA;
    }

    /**
     * Verificar si es fase de grupos + eliminación
     */
    public function esFaseGrupos(): bool
    {
        return $this === self::FASE_GRUPOS_ELIMINACION;
    }
}
