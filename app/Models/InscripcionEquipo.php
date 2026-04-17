<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InscripcionEquipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscripciones_equipo';

    protected $fillable = [
        'torneo_id',
        'categoria_id',
        'lider_jugador_id',
        'estado',
        'expires_at',
        'equipo_id',
        'cancelado_por',
        'nombre_equipo',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function torneo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Torneo::class);
    }

    public function categoria(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function lider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Jugador::class, 'lider_jugador_id');
    }

    public function equipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Equipo::class);
    }

    public function invitaciones(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvitacionJugador::class);
    }

    public function estaExpirada(): bool
    {
        return $this->expires_at !== null && $this->expires_at->isPast();
    }

    public function todasAceptadas(): bool
    {
        return $this->invitaciones()->exists()
            && $this->invitaciones()->where('estado', '!=', 'aceptada')->doesntExist();
    }
}
