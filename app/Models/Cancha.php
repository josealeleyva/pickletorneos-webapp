<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cancha extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nombre',
        'numero',
        'complejo_id',
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
        'numero' => 'integer',
    ];

    // Relaciones
    public function complejo()
    {
        return $this->belongsTo(ComplejoDeportivo::class, 'complejo_id');
    }

    public function partidos()
    {
        return $this->hasMany(Partido::class, 'cancha_id');
    }
}
