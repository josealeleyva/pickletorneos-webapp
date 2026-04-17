<?php

use App\Models\ConfiguracionSistema;

if (!function_exists('config_sistema')) {
    /**
     * Obtener configuración del sistema con caché
     *
     * Helper global para acceder a configuraciones del sistema de forma rápida.
     * Usa caché automático para evitar queries repetidas.
     *
     * @param string $clave Clave de la configuración
     * @param mixed $default Valor por defecto si no existe
     * @return mixed Valor parseado según su tipo
     *
     * @example
     * // Obtener precio de torneo
     * $precio = config_sistema('precio_torneo', 25000);
     *
     * // Obtener porcentaje de descuento
     * $descuento = config_sistema('porcentaje_descuento_referido', 20);
     */
    function config_sistema(string $clave, $default = null)
    {
        return ConfiguracionSistema::get($clave, $default);
    }
}

if (!function_exists('set_config_sistema')) {
    /**
     * Establecer o actualizar configuración del sistema
     *
     * Helper global para actualizar configuraciones. Invalida el caché automáticamente.
     *
     * @param string $clave Clave de la configuración
     * @param mixed $valor Valor a guardar
     * @param string $tipo Tipo de dato (string, integer, decimal, boolean, json)
     * @param string|null $descripcion Descripción opcional
     * @return ConfiguracionSistema
     *
     * @example
     * // Actualizar precio de torneo
     * set_config_sistema('precio_torneo', 30000, 'decimal', 'Precio base por torneo');
     *
     * // Actualizar porcentaje de descuento
     * set_config_sistema('porcentaje_descuento_referido', 25, 'integer');
     */
    function set_config_sistema(string $clave, $valor, string $tipo = 'string', ?string $descripcion = null)
    {
        return ConfiguracionSistema::set($clave, $valor, $tipo, $descripcion);
    }
}

if (!function_exists('refresh_config_sistema')) {
    /**
     * Refrescar caché de configuraciones del sistema
     *
     * Útil después de ejecutar seeders o scripts de actualización masiva.
     *
     * @return array Todas las configuraciones
     *
     * @example
     * // Después de seeders
     * refresh_config_sistema();
     */
    function refresh_config_sistema(): array
    {
        return ConfiguracionSistema::refreshCache();
    }
}
