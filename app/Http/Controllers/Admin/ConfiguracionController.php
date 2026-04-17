<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConfiguracionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfiguracionController extends Controller
{
    /**
     * Mostrar todas las configuraciones del sistema
     */
    public function index()
    {
        $configuraciones = ConfiguracionSistema::orderBy('clave')->get();

        // Agrupar configuraciones por categorías
        $categorias = [
            'precios' => [
                'titulo' => 'Precios y Pagos',
                'descripcion' => 'Configuración de precios base y políticas de pago',
                'icono' => 'dollar-sign',
                'color' => 'green',
                'claves' => ['precio_torneo'],
            ],
            'referidos' => [
                'titulo' => 'Sistema de Referidos',
                'descripcion' => 'Configuración de descuentos y bonificaciones para referidos',
                'icono' => 'users',
                'color' => 'blue',
                'claves' => ['porcentaje_descuento_referido', 'porcentaje_credito_referidor'],
            ],
        ];

        // Agrupar las configuraciones
        $configuracionesAgrupadas = [];
        foreach ($categorias as $key => $categoria) {
            $configuracionesAgrupadas[$key] = [
                'info' => $categoria,
                'configs' => $configuraciones->whereIn('clave', $categoria['claves'])->values(),
            ];
        }

        return view('admin.configuracion.index', compact('configuracionesAgrupadas'));
    }

    /**
     * Actualizar una configuración específica
     */
    public function update(Request $request, ConfiguracionSistema $configuracion)
    {
        // Validar según el tipo de configuración
        $rules = $this->getValidationRules($configuracion);

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Error al validar la configuración: ' . $validator->errors()->first());
        }

        $validated = $validator->validated();

        try {
            // Actualizar la configuración
            $configuracion->update([
                'valor' => $validated['valor'],
            ]);

            // El caché se limpia automáticamente por el evento boot() del modelo

            return back()->with('success', "Configuración '{$configuracion->clave}' actualizada correctamente");
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Error al actualizar la configuración: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Limpiar caché de configuraciones manualmente
     */
    public function clearCache()
    {
        try {
            ConfiguracionSistema::clearCache();
            ConfiguracionSistema::refreshCache();

            return back()->with('success', 'Caché de configuraciones limpiado correctamente');
        } catch (\Exception $e) {
            return back()->with('error', 'Error al limpiar el caché: ' . $e->getMessage());
        }
    }

    /**
     * Obtener reglas de validación según el tipo de configuración
     */
    private function getValidationRules(ConfiguracionSistema $configuracion): array
    {
        $baseRules = ['valor' => ['required']];

        // Reglas específicas según el tipo
        switch ($configuracion->tipo) {
            case 'integer':
                $baseRules['valor'][] = 'integer';

                // Reglas específicas por clave
                if (str_contains($configuracion->clave, 'porcentaje')) {
                    $baseRules['valor'][] = 'min:0';
                    $baseRules['valor'][] = 'max:100';
                } else {
                    $baseRules['valor'][] = 'min:0';
                }
                break;

            case 'decimal':
                $baseRules['valor'][] = 'numeric';
                $baseRules['valor'][] = 'min:0';
                break;

            case 'boolean':
                $baseRules['valor'][] = 'boolean';
                break;

            case 'json':
                $baseRules['valor'][] = 'json';
                break;

            case 'string':
            default:
                $baseRules['valor'][] = 'string';
                $baseRules['valor'][] = 'max:255';
                break;
        }

        return $baseRules;
    }

    /**
     * Obtener información de formato para mostrar en la vista
     */
    private function getFormatInfo(string $tipo): array
    {
        return match($tipo) {
            'integer' => [
                'type' => 'number',
                'step' => '1',
                'hint' => 'Número entero',
            ],
            'decimal' => [
                'type' => 'number',
                'step' => '0.01',
                'hint' => 'Número decimal',
            ],
            'boolean' => [
                'type' => 'checkbox',
                'step' => null,
                'hint' => 'Verdadero/Falso',
            ],
            default => [
                'type' => 'text',
                'step' => null,
                'hint' => 'Texto',
            ],
        };
    }
}
