<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TamanioGrupo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tamanios_grupos';

    protected $fillable = [
        'tamanio',
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
        'tamanio' => 'integer',
    ];

    // Relaciones
    public function torneos()
    {
        return $this->hasMany(Torneo::class, 'tamanio_grupo_id');
    }
}
