<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Torneo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'deporte_id',
        'descripcion',
        'fecha_inicio',
        'fecha_fin',
        'fecha_limite_inscripcion',
        'imagen_banner',
        'premios',
        'reglamento_texto',
        'reglamento_pdf',
        'complejo_id',
        'organizador_id',
        'precio_inscripcion',
        'formato_id',
        'estado',
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
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'fecha_limite_inscripcion' => 'date',
        'precio_inscripcion' => 'decimal:2',
    ];

    public function getDatosMail($partido_id, $equipo_id)
    {
        $partido = Partido::with(['equipo1.jugadores', 'equipo2.jugadores', 'cancha.complejo', 'llave'])
            ->find($partido_id);

        if (! $partido) {
            return null;
        }

        // Determinar rival según el equipo del jugador
        $equipoPropio = null;
        $equipoRival = null;

        if ($partido->equipo1_id == $equipo_id) {
            $equipoPropio = $partido->equipo1;
            $equipoRival = $partido->equipo2;
        } else {
            $equipoPropio = $partido->equipo2;
            $equipoRival = $partido->equipo1;
        }

        return [
            'deporte' => $this->deporte->nombre ?? 'Pádel',
            'complejo' => $partido->cancha->complejo->nombre ?? 'A confirmar',
            'cancha' => $partido->cancha->nombre ?? 'A confirmar',
            'fecha' => $partido->fecha_hora ? $partido->fecha_hora->locale('es')->isoFormat('dddd D [de] MMMM') : 'A confirmar',
            'hora' => $partido->fecha_hora ? $partido->fecha_hora->format('H:i').' hs' : 'A confirmar',
            'rival' => $equipoRival ? $equipoRival->nombre : 'A confirmar',
            'direccion' => $partido->cancha->complejo->direccion ?? 'A confirmar',
            'club' => $this->nombre,
            'equipo_propio' => $equipoPropio ? $equipoPropio->nombre : '',
            'instancia' => $partido->llave ? $partido->llave->ronda : 'Fase de grupos',
        ];
    }

    // Relaciones
    public function deporte()
    {
        return $this->belongsTo(Deporte::class);
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'categoria_torneo')
            ->withPivot('numero_grupos', 'tamanio_grupo_id', 'avance_grupos_id', 'cupos_categoria', 'campeon_id', 'edad_minima', 'edad_maxima', 'genero_permitido')
            ->withTimestamps();
    }

    public function complejo()
    {
        return $this->belongsTo(ComplejoDeportivo::class, 'complejo_id');
    }

    public function organizador()
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function formato()
    {
        return $this->belongsTo(FormatoTorneo::class, 'formato_id');
    }

    public function grupos()
    {
        return $this->hasMany(Grupo::class);
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function llaves()
    {
        return $this->hasMany(Llave::class);
    }

    public function partidos()
    {
        // Si el torneo es Eliminación Directa, solo partidos de llaves de este torneo
        if ($this->formato && $this->formato->esEliminacionDirecta()) {
            return Partido::whereHas('llave', function ($q) {
                $q->where('torneo_id', $this->id);
            });
        }

        // Si tiene grupos (fase + eliminación)
        if ($this->formato && $this->formato->tiene_grupos) {
            return Partido::where(function ($query) {
                $query->whereHas('grupo', function ($q) {
                    $q->where('torneo_id', $this->id);
                })->orWhereHas('llave', function ($q) {
                    $q->where('torneo_id', $this->id);
                });
            });
        }

        // Liga: partidos donde ambos equipos son del torneo y no tienen grupo ni llave
        return Partido::whereNull('grupo_id')
            ->whereNull('llave_id')
            ->where(function ($q) {
                $q->where(function ($subq) {
                    $subq->whereHas('equipo1', function ($eq) {
                        $eq->where('torneo_id', $this->id);
                    })
                        ->orWhereHas('equipo2', function ($eq) {
                            $eq->where('torneo_id', $this->id);
                        });
                });
            });
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function inscripcionesEquipo(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InscripcionEquipo::class);
    }

    public function notificaciones()
    {
        return $this->hasMany(Notificacion::class);
    }

    public function actividades()
    {
        return $this->hasMany(ActividadTorneo::class);
    }

    public function pagos()
    {
        return $this->hasMany(PagoTorneo::class);
    }

    public function pago()
    {
        return $this->hasOne(PagoTorneo::class);
    }
}
