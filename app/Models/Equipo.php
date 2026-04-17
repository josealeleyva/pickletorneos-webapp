<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'torneo_id',
        'categoria_id',
        'equipo_plantilla_id',
        'grupo_id',
        'es_cabeza_serie',
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
        'es_cabeza_serie' => 'boolean',
    ];

    // Relaciones
    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function categoria()
    {
        return $this->belongsTo(Categoria::class);
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class);
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'equipo_jugador')
            ->withPivot('orden')
            ->withTimestamps();
    }

    public function partidosComoEquipo1()
    {
        return $this->hasMany(Partido::class, 'equipo1_id');
    }

    public function partidosComoEquipo2()
    {
        return $this->hasMany(Partido::class, 'equipo2_id');
    }

    public function partidosGanados()
    {
        return $this->hasMany(Partido::class, 'equipo_ganador_id');
    }

    public function llavesComoEquipo1()
    {
        return $this->hasMany(Llave::class, 'equipo1_id');
    }

    public function llavesComoEquipo2()
    {
        return $this->hasMany(Llave::class, 'equipo2_id');
    }

    public function equipoPlantilla()
    {
        return $this->belongsTo(EquipoPlantilla::class, 'equipo_plantilla_id');
    }
}
