<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Jugador extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jugadores';

    protected $fillable = [
        'nombre',
        'apellido',
        'dni',
        'foto',
        'email',
        'telefono',
        'ranking',
        'fecha_nacimiento',
        'genero',
        'user_id',
        'organizador_id',
        'auto_aceptar_invitaciones',
        'dupr_id',
        'rating_singles',
        'rating_doubles',
        'dupr_sincronizado_at',
    ];

    protected $guarded = [
        'id',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'auto_aceptar_invitaciones' => 'boolean',
        'rating_singles' => 'float',
        'rating_doubles' => 'float',
        'dupr_sincronizado_at' => 'datetime',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $appends = [
        'nombre_completo',
    ];

    public function getDatosMail()
    {
        return [
            'nombre' => $this->nombre,
            'email' => $this->email,
        ];
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return "{$this->apellido} {$this->nombre}";
    }

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function organizador()
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function categorias()
    {
        return $this->belongsToMany(Categoria::class, 'jugadores_deportes_categorias')
            ->withPivot('deporte_id')
            ->withTimestamps();
    }

    public function equipos()
    {
        return $this->belongsToMany(Equipo::class, 'equipo_jugador')
            ->withPivot('orden')
            ->withTimestamps();
    }

    public function inscripciones()
    {
        return $this->hasMany(Inscripcion::class);
    }

    public function invitaciones()
    {
        return $this->hasMany(InvitacionJugador::class);
    }
}
