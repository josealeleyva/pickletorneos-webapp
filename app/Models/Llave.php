<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Llave extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'orden',
        'ronda',
        'equipo1_id',
        'equipo2_id',
        'torneo_id',
        'categoria_id',
        'proxima_llave_id',
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

    public function equipo1()
    {
        return $this->belongsTo(Equipo::class, 'equipo1_id');
    }

    public function equipo2()
    {
        return $this->belongsTo(Equipo::class, 'equipo2_id');
    }

    public function proximaLlave()
    {
        return $this->belongsTo(Llave::class, 'proxima_llave_id');
    }

    public function llavesAnteriores()
    {
        return $this->hasMany(Llave::class, 'proxima_llave_id');
    }

    public function partido()
    {
        return $this->hasOne(Partido::class);
    }
}
