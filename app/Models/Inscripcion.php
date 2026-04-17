<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inscripcion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'inscripciones';

    protected $fillable = [
        'torneo_id',
        'jugador_id',
        'estado',
        'fecha_inscripcion',
        'pagado',
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
        'fecha_inscripcion' => 'datetime',
        'pagado' => 'boolean',
    ];

    // Relaciones
    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function jugador()
    {
        return $this->belongsTo(Jugador::class);
    }
}
