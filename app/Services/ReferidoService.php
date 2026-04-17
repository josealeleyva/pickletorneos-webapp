<?php

namespace App\Services;

use App\Models\ConfiguracionSistema;
use App\Models\CreditoReferido;
use App\Models\PagoTorneo;
use App\Models\Referido;
use App\Models\Torneo;
use App\Models\User;

class ReferidoService
{
    /**
     * Aplicar descuento de referido en el pago del torneo
     */
    public function aplicarDescuentoReferido(User $organizador, Torneo $torneo): ?array
    {
        // Solo si fue referido
        if (!$organizador->referido_por_id) {
            return null;
        }

        // Contar torneos PAGOS previos (no incluye el gratis)
        $torneosPagosPrevios = $organizador->torneos()
            ->whereHas('pago', fn($q) => $q->where('estado', 'pagado'))
            ->count();

        // Solo aplica en el PRIMER torneo pago (segundo torneo total)
        if ($torneosPagosPrevios > 0) {
            return null;
        }

        // Verificar que no hayan pasado más de 6 meses desde el registro
        $referido = Referido::where('referido_id', $organizador->id)->first();

        if (!$referido || $referido->fecha_registro->addMonths(6)->isPast()) {
            return null; // Expiró el beneficio
        }

        $precioBase = ConfiguracionSistema::get('precio_torneo', 25000);
        $porcentajeDescuento = ConfiguracionSistema::get('porcentaje_descuento_referido', 20);
        $montoDescuento = $precioBase * ($porcentajeDescuento / 100); // Descuento calculado

        return [
            'precio_original' => $precioBase,
            'porcentaje_descuento' => $porcentajeDescuento,
            'monto_descuento' => $montoDescuento,
            'precio_final' => $precioBase - $montoDescuento, // $20.000
        ];
    }

    /**
     * Verificar si el referido activó su cuenta (pagó segundo torneo)
     * y acreditar torneo gratis al referidor
     */
    public function verificarActivacionReferido(PagoTorneo $pago)
    {
        $organizador = $pago->torneo->organizador;

        // Verificar si es referido
        $referido = Referido::where('referido_id', $organizador->id)
            ->where('estado', 'pendiente')
            ->first();

        if (!$referido) {
            return;
        }

        // Contar torneos pagos (después de este pago)
        $torneosPagos = $organizador->torneos()
            ->whereHas('pago', fn($q) => $q->where('estado', 'pagado'))
            ->count();

        // Si este es el primer torneo pago (segundo torneo total), activar
        if ($torneosPagos === 1) {
            $referido->activar();

            // Notificar al referidor
            $referido->referidor->notify(
                new \App\Notifications\ReferidoActivadoNotification($referido)
            );
        }
    }

    /**
     * Obtener crédito disponible para usar en un torneo
     */
    public function obtenerCreditoDisponible(User $user): ?CreditoReferido
    {
        return $user->creditosDisponibles()->oldest('fecha_acreditacion')->first();
    }

    /**
     * Usar crédito de referido en un torneo (torneo gratis)
     */
    public function usarCreditoReferido(User $user, Torneo $torneo): bool
    {
        $credito = $this->obtenerCreditoDisponible($user);

        if (!$credito) {
            return false;
        }

        // Marcar crédito como usado
        $credito->usar($torneo);

        // Actualizar o crear pago como "gratuito" con referencia al crédito
        PagoTorneo::updateOrCreate(
            ['torneo_id' => $torneo->id],
            [
                'organizador_id' => $torneo->organizador_id,
                'monto' => 0,
                'estado' => 'gratuito',
                'credito_referido_id' => $credito->id,
            ]
        );

        return true;
    }
}
