<?php

namespace App\Models;

use App\Enums\TipoFormatoTorneo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormatoTorneo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'formatos_torneos';

    protected $fillable = [
        'nombre',
        'tiene_grupos',
    ];

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'tiene_grupos' => 'boolean',
    ];

    // Relaciones
    public function torneos()
    {
        return $this->hasMany(Torneo::class, 'formato_id');
    }

    // Métodos helper

    /**
     * Obtener el tipo de formato como enum
     */
    public function getTipo(): ?TipoFormatoTorneo
    {
        return TipoFormatoTorneo::tryFrom($this->nombre);
    }

    /**
     * Verificar si el formato es Eliminación Directa
     */
    public function esEliminacionDirecta(): bool
    {
        return $this->getTipo() === TipoFormatoTorneo::ELIMINACION_DIRECTA;
    }

    /**
     * Verificar si el formato es Liga (anteriormente Round Robin)
     */
    public function esLiga(): bool
    {
        return $this->getTipo() === TipoFormatoTorneo::LIGA;
    }

    /**
     * Verificar si el formato es Fase de Grupos + Eliminación
     */
    public function esFaseGrupos(): bool
    {
        return $this->getTipo() === TipoFormatoTorneo::FASE_GRUPOS_ELIMINACION;
    }
}
