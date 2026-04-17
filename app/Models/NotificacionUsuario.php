<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificacionUsuario extends Model
{
    use HasFactory;

    protected $table = 'notificaciones_usuarios';

    protected $fillable = [
        'notificacion_id',
        'user_id',
        'leida',
        'leida_at',
    ];

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'leida' => 'boolean',
        'leida_at' => 'datetime',
    ];

    // Relaciones
    public function notificacion()
    {
        return $this->belongsTo(Notificacion::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
