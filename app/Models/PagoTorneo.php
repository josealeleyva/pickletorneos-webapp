<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PagoTorneo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pagos_torneos';

    protected $fillable = [
        'torneo_id',
        'organizador_id',
        'monto',
        'estado',
        'referencia_pago',
        'metodo_pago',
        'pagado_en',
        'es_primer_torneo_gratis',
        'credito_referido_id',
        'notas',
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
        'monto' => 'decimal:2',
        'pagado_en' => 'datetime',
        'es_primer_torneo_gratis' => 'boolean',
    ];

    // Relaciones
    public function torneo()
    {
        return $this->belongsTo(Torneo::class);
    }

    public function organizador()
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }
}
