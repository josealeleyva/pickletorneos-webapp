<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AvanceGrupo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'avances_grupos';

    protected $fillable = [
        'nombre',
        'cantidad_avanza_directo',
        'cantidad_avanza_mejores',
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
        'cantidad_avanza_directo' => 'integer',
        'cantidad_avanza_mejores' => 'integer',
    ];

    // Relaciones
    public function torneos()
    {
        return $this->hasMany(Torneo::class, 'avance_grupos_id');
    }
}
