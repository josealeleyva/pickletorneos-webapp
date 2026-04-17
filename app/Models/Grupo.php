<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Grupo extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'orden',
        'torneo_id',
        'categoria_id',
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
        'orden' => 'integer',
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

    public function equipos()
    {
        return $this->hasMany(Equipo::class);
    }

    public function partidos()
    {
        return $this->hasMany(Partido::class);
    }
}
