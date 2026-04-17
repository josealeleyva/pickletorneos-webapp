<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Categoria extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'deporte_id',
        'organizador_id',
        'nombre',
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
    public function deporte()
    {
        return $this->belongsTo(Deporte::class);
    }

    public function organizador()
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function jugadores()
    {
        return $this->belongsToMany(Jugador::class, 'jugadores_deportes_categorias')
            ->withPivot('deporte_id')
            ->withTimestamps();
    }

    public function torneos()
    {
        return $this->belongsToMany(Torneo::class, 'categoria_torneo');
    }

    // Scopes
    public function scopeDelOrganizador($query, $organizadorId)
    {
        return $query->where('organizador_id', $organizadorId);
    }
}
