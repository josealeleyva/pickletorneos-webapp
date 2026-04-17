<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActividadTorneo extends Model
{
    use HasFactory;

    protected $table = 'actividades_torneo';

    protected $fillable = [
        'torneo_id',
        'user_id',
        'tipo',
        'descripcion',
        'datos',
    ];

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'datos' => 'array',
    ];

    // Relaciones
    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
