<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditoReferido extends Model
{
    use HasFactory;

    protected $table = 'creditos_referidos';

    protected $fillable = [
        'user_id',
        'referido_id',
        'monto',
        'estado',
        'fecha_acreditacion',
        'fecha_vencimiento',
        'torneo_usado_id',
        'fecha_uso',
        'notas',
    ];

    protected $casts = [
        'fecha_acreditacion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
        'fecha_uso' => 'datetime',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function referido()
    {
        return $this->belongsTo(User::class, 'referido_id');
    }

    public function torneoUsado()
    {
        return $this->belongsTo(Torneo::class, 'torneo_usado_id');
    }

    /**
     * Usar el crédito en un torneo
     */
    public function usar(Torneo $torneo)
    {
        $this->update([
            'estado' => 'usado',
            'torneo_usado_id' => $torneo->id,
            'fecha_uso' => now(),
        ]);
    }

    /**
     * Scope para créditos vigentes
     */
    public function scopeVigentes($query)
    {
        return $query->where('estado', 'disponible')
            ->where('fecha_vencimiento', '>', now());
    }
}
