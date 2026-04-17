<?php

namespace App\Http\Controllers;

use App\Models\Torneo;
use App\Models\PagoTorneo;
use App\Models\ConfiguracionSistema;
use App\Services\ReferidoService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;

class PagoController extends Controller
{
    public function __construct()
    {
        // Configurar MercadoPago SDK v3
        MercadoPagoConfig::setAccessToken(config('mercadopago.access_token'));
    }

    /**
     * Mostrar página de checkout para un torneo
     */
    public function checkout(Torneo $torneo, ReferidoService $referidoService)
    {
        $this->authorize('update', $torneo);

        // Verificar que el torneo pertenece al usuario autenticado
        if ($torneo->organizador_id !== Auth::id()) {
            abort(403);
        }

        $organizador = Auth::user();

        // Obtener el pago del torneo
        $pago = PagoTorneo::where('torneo_id', $torneo->id)->first();

        if (!$pago) {
            return redirect()
                ->route('torneos.index')
                ->with('error', 'No se encontró información de pago para este torneo.');
        }

        // Si ya está pagado, redirigir al torneo
        if ($pago->estado === 'pagado' || $pago->estado === 'gratuito') {
            return redirect()
                ->route('torneos.show', $torneo)
                ->with('info', 'Este torneo ya está pagado.');
        }

        // Verificar si tiene créditos de referido disponibles
        $creditoDisponible = $referidoService->obtenerCreditoDisponible($organizador);

        // Verificar si aplica descuento de referido
        $descuentoReferido = $referidoService->aplicarDescuentoReferido($organizador, $torneo);

        // Calcular precio final
        $precioBase = ConfiguracionSistema::get('precio_torneo', 25000);
        $precioFinal = $precioBase;

        if ($descuentoReferido) {
            $precioFinal = $descuentoReferido['precio_final'];
        }

        // Verificar que el access token esté configurado
        $accessToken = config('mercadopago.access_token');

        if (empty($accessToken)) {
            \Log::error('MercadoPago access token no configurado');
            return redirect()
                ->route('torneos.index')
                ->with('error', 'Error de configuración: MercadoPago no está configurado correctamente. Contacta al administrador.');
        }

        try {
            // Crear preferencia de pago en MercadoPago v3
            $client = new PreferenceClient();

            $preference = $client->create([
                "items" => [
                    [
                        "title" => "Torneo: {$torneo->nombre}",
                        "description" => "Pago por creación de torneo en Punto de Oro",
                        "quantity" => 1,
                        "unit_price" => (float) $precioFinal,
                    ]
                ],
                "back_urls" => [
                    "success" => url()->route('pagos.success', $torneo, true), // URL absoluta
                    "failure" => url()->route('pagos.failure', $torneo, true),
                    "pending" => url()->route('pagos.pending', $torneo, true),
                ],
                "auto_return" => "approved",
                "external_reference" => "torneo_{$torneo->id}_pago_{$pago->id}",
                "metadata" => [
                    "torneo_id" => (string) $torneo->id,
                    "pago_id" => (string) $pago->id,
                    "organizador_id" => (string) Auth::id(),
                ],
            ]);

            // Actualizar referencia en el pago
            $pago->update([
                'referencia_pago' => $preference->id,
            ]);

            return view('pagos.checkout', compact(
                'torneo',
                'pago',
                'preference',
                'precioBase',
                'precioFinal',
                'descuentoReferido',
                'creditoDisponible'
            ));

        } catch (\MercadoPago\Exceptions\MPApiException $e) {
            $apiResponse = $e->getApiResponse();
            $content = $apiResponse ? $apiResponse->getContent() : null;

            \Log::error('Error de MercadoPago API', [
                'message' => $e->getMessage(),
                'status_code' => $apiResponse ? $apiResponse->getStatusCode() : null,
                'content' => $content,
                'access_token_configured' => !empty($accessToken),
                'access_token_length' => strlen($accessToken ?? ''),
            ]);

            // Marcar el pago como cancelado si falló
            $pago->update(['estado' => 'cancelado']);

            return redirect()
                ->route('torneos.index')
                ->with('error', 'Error al conectar con MercadoPago. Verifica que las credenciales estén configuradas correctamente. Si el problema persiste, contacta al administrador.');

        } catch (\Exception $e) {
            \Log::error('Error inesperado al crear preferencia de pago', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Marcar el pago como cancelado si falló
            $pago->update(['estado' => 'cancelado']);

            return redirect()
                ->route('torneos.index')
                ->with('error', 'Error inesperado al procesar el pago. Por favor, intenta nuevamente.');
        }
    }

    /**
     * Pago exitoso
     */
    public function success(Request $request, Torneo $torneo, ReferidoService $referidoService)
    {
        $paymentId = $request->query('payment_id');
        $status = $request->query('status');

        $pago = PagoTorneo::where('torneo_id', $torneo->id)->first();

        if (!$pago) {
            return redirect()
                ->route('torneos.index')
                ->with('error', 'No se encontró información de pago.');
        }

        // Actualizar estado del pago (será confirmado por el webhook)
        if ($status === 'approved' && $pago->estado === 'pendiente') {
            $pago->update([
                'estado' => 'pagado',
                'pagado_en' => now(),
                'referencia_pago' => $paymentId,
            ]);

            // Pasar el torneo a activo automáticamente
            if ($torneo->estado === 'borrador') {
                $torneo->update(['estado' => 'activo']);
            }

            // Verificar si el referido activó su cuenta
            $referidoService->verificarActivacionReferido($pago);
        }

        return view('pagos.success', compact('torneo', 'pago'));
    }

    /**
     * Pago pendiente
     */
    public function pending(Request $request, Torneo $torneo)
    {
        $pago = PagoTorneo::where('torneo_id', $torneo->id)->first();
        return view('pagos.pending', compact('torneo', 'pago'));
    }

    /**
     * Pago fallido
     */
    public function failure(Request $request, Torneo $torneo)
    {
        $pago = PagoTorneo::where('torneo_id', $torneo->id)->first();
        return view('pagos.failure', compact('torneo', 'pago'));
    }

    /**
     * Usar crédito de referido para pagar torneo gratis
     */
    public function usarCredito(Torneo $torneo, ReferidoService $referidoService)
    {
        $this->authorize('update', $torneo);

        $organizador = Auth::user();

        // Verificar que el torneo pertenece al usuario autenticado
        if ($torneo->organizador_id !== $organizador->id) {
            abort(403);
        }

        // Obtener el pago del torneo
        $pago = PagoTorneo::where('torneo_id', $torneo->id)->first();

        if (!$pago || $pago->estado !== 'pendiente') {
            return redirect()
                ->route('torneos.index')
                ->with('error', 'Este torneo no está en estado pendiente de pago.');
        }

        // Intentar usar el crédito
        $exito = $referidoService->usarCreditoReferido($organizador, $torneo);

        if (!$exito) {
            return redirect()
                ->route('pagos.checkout', $torneo)
                ->with('error', 'No tienes créditos disponibles para usar.');
        }

        // Pasar el torneo a activo automáticamente
        if ($torneo->estado === 'borrador') {
            $torneo->update(['estado' => 'activo']);
        }

        return redirect()
            ->route('torneos.show', $torneo)
            ->with('success', '¡Torneo creado gratis usando tu crédito de referido!');
    }

    /**
     * Webhook de MercadoPago
     */
    public function webhook(Request $request)
    {
        // Log para debugging
        \Log::info('Webhook MercadoPago recibido', $request->all());

        $type = $request->input('type');
        $data = $request->input('data');

        if ($type === 'payment') {
            $paymentId = $data['id'] ?? null;

            if ($paymentId) {
                // Consultar el pago en MercadoPago usando SDK v3
                try {
                    $client = new PaymentClient();
                    $payment = $client->get($paymentId);

                    if ($payment) {
                        // Obtener metadata
                        $metadata = $payment->metadata ?? [];
                        $pagoId = $metadata['pago_id'] ?? null;

                        if ($pagoId) {
                            $pago = PagoTorneo::find($pagoId);

                            if ($pago && $pago->estado === 'pendiente') {
                                // Actualizar según el estado del pago
                                if ($payment->status === 'approved') {
                                    $pago->update([
                                        'estado' => 'pagado',
                                        'pagado_en' => now(),
                                        'referencia_pago' => $paymentId,
                                    ]);

                                    \Log::info("Pago {$pagoId} confirmado exitosamente");

                                    // Pasar el torneo a activo automáticamente
                                    $pago->torneo?->update(['estado' => 'activo']);

                                    // Verificar activación del referido
                                    $referidoService = app(ReferidoService::class);
                                    $referidoService->verificarActivacionReferido($pago);
                                } elseif ($payment->status === 'rejected') {
                                    $pago->update([
                                        'estado' => 'cancelado',
                                    ]);

                                    \Log::info("Pago {$pagoId} rechazado");
                                }
                            }
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error('Error procesando webhook de MercadoPago: ' . $e->getMessage());
                    return response()->json(['error' => 'Error procesando pago'], 500);
                }
            }
        }

        return response()->json(['status' => 'ok'], 200);
    }
}
