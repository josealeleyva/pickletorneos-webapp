<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notificacion extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'notificaciones';

    protected $fillable = [
        'torneo_id',
        'mensaje',
        'tipo',
        'enviado_at',
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
        'enviado_at' => 'datetime',
    ];

    // Relaciones
    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function usuarios()
    {
        return $this->belongsToMany(User::class, 'notificaciones_usuarios')
            ->withPivot('leida', 'leida_at')
            ->withTimestamps();
    }

    public function notificacionesUsuarios()
    {
        return $this->hasMany(NotificacionUsuario::class);
    }
}
