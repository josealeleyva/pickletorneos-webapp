<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ResultadoTentativo extends Model
{
    use HasFactory;

    protected $table = 'resultados_tentativo';

    protected $fillable = [
        'partido_id',
        'propuesto_por_equipo_id',
        'propuesto_por_jugador_id',
        'juegos',
        'sets_equipo1',
        'sets_equipo2',
        'equipo_ganador_id',
    ];

    protected $casts = [
        'juegos' => 'array',
        'sets_equipo1' => 'integer',
        'sets_equipo2' => 'integer',
    ];

    public function partido()
    {
        return $this->belongsTo(Partido::class);
    }

    public function propuestoPorEquipo()
    {
        return $this->belongsTo(Equipo::class, 'propuesto_por_equipo_id');
    }

    public function propuestoPorJugador()
    {
        return $this->belongsTo(Jugador::class, 'propuesto_por_jugador_id');
    }

    public function equipoGanador()
    {
        return $this->belongsTo(Equipo::class, 'equipo_ganador_id');
    }
}
