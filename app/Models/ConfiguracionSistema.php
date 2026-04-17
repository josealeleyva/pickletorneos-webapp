<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class ConfiguracionSistema extends Model
{
    use HasFactory;

    protected $table = 'configuracion_sistema';

    protected $fillable = [
        'clave',
        'valor',
        'tipo',
        'descripcion',
    ];

    /**
     * Clave de caché para todas las configuraciones
     */
    const CACHE_KEY = 'sistema_configuraciones_all';

    /**
     * Tiempo de caché en segundos (24 horas = 86400 segundos)
     */
    const CACHE_TTL = 86400;

    /**
     * Boot del modelo - invalidar caché cuando se actualiza
     */
    protected static function boot()
    {
        parent::boot();

        // Invalidar caché cuando se crea, actualiza o elimina una configuración
        static::saved(function () {
            static::clearCache();
        });

        static::deleted(function () {
            static::clearCache();
        });
    }

    /**
     * Obtener valor de configuración parseado según su tipo
     */
    public function getValorParsedAttribute()
    {
        return match($this->tipo) {
            'integer' => (int) $this->valor,
            'decimal' => (float) $this->valor,
            'boolean' => filter_var($this->valor, FILTER_VALIDATE_BOOLEAN),
            'json' => json_decode($this->valor, true),
            default => $this->valor,
        };
    }

    /**
     * Helper estático para obtener configuración con caché
     *
     * ✅ OPTIMIZADO: Usa caché para evitar queries repetidas
     *
     * @param string $clave Clave de la configuración
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor parseado según su tipo
     */
    public static function get(string $clave, $default = null)
    {
        // Obtener todas las configuraciones en caché (1 query en lugar de N)
        $configuraciones = static::getAllCached();

        // Buscar la configuración en el array
        if (isset($configuraciones[$clave])) {
            return $configuraciones[$clave];
        }

        return $default;
    }

    /**
     * Obtener todas las configuraciones en caché
     *
     * Este método carga todas las configuraciones de una sola vez
     * y las mantiene en caché por 24 horas.
     *
     * @return array Array asociativo [clave => valor_parsed]
     */
    public static function getAllCached(): array
    {
        return Cache::remember(static::CACHE_KEY, static::CACHE_TTL, function () {
            return static::all()->pluck('valor_parsed', 'clave')->toArray();
        });
    }

    /**
     * Limpiar caché de configuraciones
     *
     * Llamar este método después de actualizar configuraciones
     * para forzar una recarga desde la base de datos.
     *
     * @return void
     */
    public static function clearCache(): void
    {
        Cache::forget(static::CACHE_KEY);
    }

    /**
     * Actualizar o crear una configuración e invalidar caché
     *
     * @param string $clave
     * @param mixed $valor
     * @param string $tipo
     * @param string|null $descripcion
     * @return static
     */
    public static function set(string $clave, $valor, string $tipo = 'string', ?string $descripcion = null)
    {
        $config = static::updateOrCreate(
            ['clave' => $clave],
            [
                'valor' => $valor,
                'tipo' => $tipo,
                'descripcion' => $descripcion,
            ]
        );

        // El caché se limpia automáticamente por el evento boot()
        return $config;
    }

    /**
     * Refrescar todas las configuraciones en caché
     *
     * Útil para pre-cargar el caché después de seeders o en warmup
     *
     * @return array
     */
    public static function refreshCache(): array
    {
        static::clearCache();
        return static::getAllCached();
    }
}
