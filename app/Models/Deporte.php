<?php

namespace App\Models;

use App\Enums\TipoDeporte;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deporte extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
    ];

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relaciones
    public function categorias()
    {
        return $this->hasMany(Categoria::class);
    }

    public function torneos()
    {
        return $this->hasMany(Torneo::class);
    }

    public function users()
    {
        return $this->hasMany(User::class, 'deporte_principal_id');
    }

    // Métodos helper

    /**
     * Obtener el tipo de deporte como enum
     */
    public function getTipo(): ?TipoDeporte
    {
        return TipoDeporte::tryFrom($this->nombre);
    }

    /**
     * Obtener el máximo de jugadores permitidos por equipo
     */
    public function getMaxJugadores(): int
    {
        return $this->getTipo()?->getMaxJugadores() ?? 1;
    }

    /**
     * Determinar si el deporte requiere nombre de equipo personalizado
     */
    public function requiereNombreEquipo(): bool
    {
        return $this->getTipo()?->requiereNombreEquipo() ?? true;
    }

    public function esFutbol(): bool
    {
        return $this->getTipo()?->esFutbol() ?? false;
    }

    /**
     * Determinar si el deporte usa sistema de sets/juegos
     */
    public function usaSets(): bool
    {
        return $this->getTipo()?->usaSets() ?? false;
    }

    /**
     * Determinar si el deporte permite empates
     * Solo Fútbol permite empates, Padel y Tenis siempre tienen ganador
     */
    public function permiteEmpates(): bool
    {
        return $this->getTipo()?->permiteEmpates() ?? false;
    }
}
