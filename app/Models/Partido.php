<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partido extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'fecha_hora',
        'equipo1_id',
        'equipo2_id',
        'cancha_id',
        'grupo_id',
        'llave_id',
        'equipo_ganador_id',
        'sets_equipo1',
        'sets_equipo2',
        'estado',
        'observaciones',
        'notificado',
        'ultima_notificacion',
        'dupr_partido_id',
        'dupr_sincronizado',
        'dupr_sincronizado_at',
        'dupr_error',
    ];

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'deleted_at',
    ];

    protected $casts = [
        'fecha_hora' => 'datetime',
        'sets_equipo1' => 'integer',
        'sets_equipo2' => 'integer',
        'notificado' => 'boolean',
        'ultima_notificacion' => 'datetime',
        'updated_at' => 'datetime',
        'dupr_sincronizado' => 'boolean',
        'dupr_sincronizado_at' => 'datetime',
    ];

    // Relaciones
    public function equipo1()
    {
        return $this->belongsTo(Equipo::class, 'equipo1_id');
    }

    public function equipo2()
    {
        return $this->belongsTo(Equipo::class, 'equipo2_id');
    }

    public function equipoGanador()
    {
        return $this->belongsTo(Equipo::class, 'equipo_ganador_id');
    }

    public function cancha()
    {
        return $this->belongsTo(Cancha::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function llave()
    {
        return $this->belongsTo(Llave::class);
    }

    public function juegos()
    {
        return $this->hasMany(Juego::class);
    }

    public function resultadoTentativo()
    {
        return $this->hasOne(ResultadoTentativo::class);
    }

}
