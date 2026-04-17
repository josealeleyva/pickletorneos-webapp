<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Juego extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'partido_id',
        'numero_juego',
        'juegos_equipo1',
        'juegos_equipo2',
        'equipo_ganador_id',
        'orden',
        'tipo_juego', // ✅ NUEVO: set, partido, ida, vuelta, penales
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
        'juegos_equipo1' => 'integer',
        'juegos_equipo2' => 'integer',
        'numero_juego' => 'integer',
        'orden' => 'integer',
    ];

    // Relaciones
    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function equipoGanador()
    {
        return $this->belongsTo(Equipo::class, 'equipo_ganador_id');
    }
}
