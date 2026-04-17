<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EquipoPlantilla extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'organizador_id',
        'deporte_id',
        'ultima_formacion',
        'veces_usado',
        'ultimo_uso',
    ];

    protected $casts = [
        'ultima_formacion' => 'array',
        'veces_usado' => 'integer',
        'ultimo_uso' => 'datetime',
    ];

    // Relaciones
    public function organizador()
    {
        return $this->belongsTo(User::class, 'organizador_id');
    }

    public function deporte()
    {
        return $this->belongsTo(Deporte::class);
    }

    public function equipos()
    {
        return $this->hasMany(Equipo::class, 'equipo_plantilla_id');
    }

    // Métodos helper
    public function incrementarUso()
    {
        $this->increment('veces_usado');
        $this->update(['ultimo_uso' => now()]);
    }

    public function actualizarFormacion(array $jugadoresIds)
    {
        $this->update([
            'ultima_formacion' => $jugadoresIds,
            'ultimo_uso' => now(),
        ]);
        $this->increment('veces_usado');
    }
}
