<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ComplejoDeportivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'complejos_deportivos';

    protected $fillable = [
        'nombre',
        'direccion',
        'latitud',
        'longitud',
        'telefono',
        'email',
        'organizador_id',
    ];

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    // Relaciones
    public function organizador()
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function canchas()
    {
        return $this->hasMany(Cancha::class, 'complejo_id');
    }

    public function torneos()
    {
        return $this->hasMany(Torneo::class, 'complejo_id');
    }
}
