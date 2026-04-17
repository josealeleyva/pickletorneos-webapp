<?php

namespace App\Enums;

enum TipoDeporte: string
{
    case PADEL = 'Padel';
    case TENIS = 'Tenis';
    case FUTBOL = 'Futbol';
    case PICKLEBALL = 'Pickleball';

    /**
     * Obtener el máximo de jugadores permitidos por equipo
     */
    public function getMaxJugadores(): int
    {
        return match ($this) {
            self::PADEL => 2,
            self::TENIS => 2,
            self::FUTBOL => 23,
            self::PICKLEBALL => 2,
        };
    }

    /**
     * Determinar si el deporte requiere nombre de equipo personalizado
     * Deportes individuales/dobles (Padel, Tenis) no lo requieren
     */
    public function requiereNombreEquipo(): bool
    {
        return match ($this) {
            self::PADEL, self::TENIS, self::PICKLEBALL => false,
            self::FUTBOL => true,
        };
    }
    public function esFutbol(): bool
    {
        return match ($this) {
            self::PADEL, self::TENIS, self::PICKLEBALL => false,
            self::FUTBOL => true,
        };
    }

    /**
     * Determinar si el deporte usa sistema de sets/juegos
     * Padel y Tenis usan sets, Fútbol no
     */
    public function usaSets(): bool
    {
        return match ($this) {
            self::PADEL, self::TENIS, self::PICKLEBALL => true,
            self::FUTBOL => false,
        };
    }

    /**
     * Determinar si el deporte permite empates
     * Solo Fútbol permite empates, Padel y Tenis siempre tienen ganador
     */
    public function permiteEmpates(): bool
    {
        return match ($this) {
            self::PADEL, self::TENIS, self::PICKLEBALL => false,
            self::FUTBOL => true,
        };
    }

    /**
     * Obtener todos los valores de deportes
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
}
