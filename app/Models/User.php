<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'nombre',
        'apellido',
        'email',
        'password',
        'telefono',
        'foto',
        'deporte_principal_id',
        'organizacion',
        'cuenta_activa',
        'torneos_creados',
        'codigo_referido',
        'referido_por_id',
        'total_referidos_activos',
        'google_id',
        'dupr_access_token',
        'dupr_token_expires_at',
    ];

    protected $guarded = [
        'id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'cuenta_activa' => 'boolean',
        'torneos_creados' => 'integer',
        'dupr_token_expires_at' => 'datetime',
    ];

    protected $with = [
        'roles',
        'roles.permissions',
    ];

    // Relaciones
    public function deportePrincipal()
    {
        return $this->belongsTo(Deporte::class, 'deporte_principal_id');
    }

    public function jugador()
    {
        return $this->hasOne(Jugador::class);
    }

    public function complejos()
    {
        return $this->hasMany(ComplejoDeportivo::class, 'organizador_id');
    }

    public function torneos()
    {
        return $this->hasMany(Torneo::class, 'organizador_id');
    }

    public function categorias()
    {
        return $this->hasMany(Categoria::class, 'organizador_id');
    }

    public function pagosTorneos()
    {
        return $this->hasMany(PagoTorneo::class, 'organizador_id');
    }

    public function jugadores()
    {
        return $this->hasMany(Jugador::class, 'organizador_id');
    }

    public function actividadesTorneo()
    {
        return $this->hasMany(ActividadTorneo::class);
    }

    public function notificaciones()
    {
        return $this->belongsToMany(Notificacion::class, 'notificaciones_usuarios')
            ->withPivot('leida', 'leida_at')
            ->withTimestamps();
    }

    // Relaciones de Referidos

    /**
     * Usuarios que este usuario refirió
     */
    public function referidos()
    {
        return $this->hasMany(User::class, 'referido_por_id');
    }

    /**
     * Quien refirió a este usuario
     */
    public function referidor()
    {
        return $this->belongsTo(User::class, 'referido_por_id');
    }

    /**
     * Créditos de referidos disponibles
     */
    public function creditosReferidos()
    {
        return $this->hasMany(CreditoReferido::class, 'user_id');
    }

    /**
     * Créditos disponibles (no usados ni expirados)
     */
    public function creditosDisponibles()
    {
        return $this->creditosReferidos()
            ->where('estado', 'disponible')
            ->where('fecha_vencimiento', '>', now());
    }

    /**
     * Generar código de referido único
     */
    public function generarCodigoReferido(): string
    {
        if ($this->codigo_referido) {
            return $this->codigo_referido;
        }

        do {
            $codigo = 'PT'.strtoupper(\Illuminate\Support\Str::random(6));
        } while (User::where('codigo_referido', $codigo)->exists());

        $this->update(['codigo_referido' => $codigo]);

        return $codigo;
    }

    /**
     * Saldo total de créditos disponibles
     */
    public function getSaldoCreditosAttribute(): float
    {
        return $this->creditosDisponibles()->sum('monto');
    }

    /**
     * Cantidad de créditos (torneos gratis) disponibles
     */
    public function getCantidadCreditosAttribute(): int
    {
        return $this->creditosDisponibles()->count();
    }

    /**
     * Accessor para nombre (compatibilidad con name)
     */
    public function getNombreAttribute()
    {
        // Si existe el atributo nombre, usarlo, sino usar name
        if (array_key_exists('nombre', $this->attributes) && $this->attributes['nombre']) {
            return $this->attributes['nombre'];
        }

        return $this->attributes['name'] ?? '';
    }
}
