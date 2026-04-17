<?php

namespace App\Http\Middleware;

use Closure;

class SanitizeInput
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Recorrer todas las entradas y sanitizarlas
        $sanitized = $this->sanitize($request->all());

        // Reemplazar los datos de la request con los datos sanitizados
        $request->merge($sanitized);

        return $next($request);
    }

    /**
     * Sanitizar los datos de la request.
     *
     * @param array $input
     * @return array
     */
    protected function sanitize(array $input)
    {
        // Iterar sobre cada entrada y sanitizar
        return array_map(function ($value) {
            // Aplicar la función nativa de PHP para limpiar strings
            return is_string($value) ? filter_var($value, FILTER_SANITIZE_STRING) : $value;
        }, $input);
    }
}
