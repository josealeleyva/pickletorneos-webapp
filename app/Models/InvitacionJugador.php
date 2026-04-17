<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitacionJugador extends Model
{
    use HasFactory;

    protected $table = 'invitaciones_jugador';

    protected $fillable = [
        'inscripcion_equipo_id',
        'jugador_id',
        'estado',
        'auto_aceptada',
        'token',
        'respondido_at',
    ];

    protected $casts = [
        'auto_aceptada' => 'boolean',
        'respondido_at' => 'datetime',
    ];

    public function inscripcionEquipo(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(InscripcionEquipo::class);
    }

    public function jugador(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Jugador::class);
    }
}
