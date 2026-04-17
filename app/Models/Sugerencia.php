<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sugerencia extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'sugerencias';

    protected $fillable = [
        'user_id',
        'tipo',
        'asunto',
        'mensaje',
        'estado',
        'respuesta',
        'respondida_en',
        'respondida_por',
    ];

    protected $casts = [
        'respondida_en' => 'datetime',
    ];

    // Relaciones
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function respondidaPor()
    {
        return $this->belongsTo(User::class, 'respondida_por');
    }

    // Scopes
    public function scopeNuevas($query)
    {
        return $query->where('estado', 'nueva');
    }

    public function scopeEnRevision($query)
    {
        return $query->where('estado', 'en_revision');
    }

    public function scopePendientes($query)
    {
        return $query->whereIn('estado', ['nueva', 'en_revision']);
    }
}
