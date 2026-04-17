<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Referido extends Model
{
    use HasFactory;

    protected $fillable = [
        'referidor_id',
        'referido_id',
        'fecha_registro',
        'estado',
        'fecha_activacion',
    ];

    protected $casts = [
        'fecha_registro' => 'datetime',
        'fecha_activacion' => 'datetime',
    ];

    public function referidor()
    {
        return $this->belongsTo(User::class, 'referidor_id');
    }

    public function referido()
    {
        return $this->belongsTo(User::class, 'referido_id');
    }

    /**
     * Marcar como activo cuando paga su segundo torneo
     */
    public function activar()
    {
        $this->update([
            'estado' => 'activo',
            'fecha_activacion' => now(),
        ]);

        // Acreditar torneo gratis al referidor
        $precioTorneo = ConfiguracionSistema::get('precio_torneo', 25000);
        $porcentajeCreditoReferidor = ConfiguracionSistema::get('porcentaje_credito_referidor', 100);
        $montoCreditoReferidor = $precioTorneo * ($porcentajeCreditoReferidor / 100);

        CreditoReferido::create([
            'user_id' => $this->referidor_id,
            'referido_id' => $this->referido_id,
            'monto' => $montoCreditoReferidor,
            'estado' => 'disponible',
            'fecha_acreditacion' => now(),
            'fecha_vencimiento' => now()->addMonths(12),
        ]);

        // Actualizar contador de referidos activos
        $this->referidor->increment('total_referidos_activos');
    }
}
