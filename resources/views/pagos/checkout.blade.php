@extends('layouts.dashboard')

@section('title', 'Pago del Torneo')
@section('page-title', 'Completar Pago')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-yellow-600">
        <div class="flex items-start gap-4 mb-6">
            <div class="flex-shrink-0">
                <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="flex-1">
                <h3 class="text-xl font-bold text-gray-900 mb-2">
                    Pago Requerido
                </h3>
                <p class="text-gray-600 text-sm mb-4">
                    Para continuar con la configuración del torneo "{{ $torneo->nombre }}", debes completar el pago.
                </p>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="flex justify-between items-center mb-3">
                <span class="text-gray-700 font-medium">Torneo:</span>
                <span class="text-gray-900 font-semibold">{{ $torneo->nombre }}</span>
            </div>
            <div class="flex justify-between items-center mb-3">
                <span class="text-gray-700 font-medium">Deporte:</span>
                <span class="text-gray-900">{{ $torneo->deporte->nombre }}</span>
            </div>
            <div class="border-t border-gray-200 my-3"></div>

            @if($descuentoReferido)
                <!-- Mostrar descuento de referido -->
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-700 font-medium">Precio del torneo:</span>
                    <span class="text-gray-500 line-through">${{ number_format($precioBase, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-3">
                    <span class="text-green-700 font-medium">
                        <svg class="w-4 h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Descuento de referido ({{ $descuentoReferido['porcentaje_descuento'] }}%):
                    </span>
                    <span class="text-green-700 font-medium">-${{ number_format($descuentoReferido['monto_descuento'], 0, ',', '.') }}</span>
                </div>
                <div class="border-t border-gray-200 my-3"></div>
            @endif

            <div class="flex justify-between items-center">
                <span class="text-lg font-bold text-gray-900">Total a pagar:</span>
                <span class="text-2xl font-bold text-brand-600">${{ number_format($precioFinal, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($creditoDisponible)
            <!-- Opción de usar crédito gratis -->
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-2 border-green-500 rounded-lg p-5 mb-6">
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-8 h-8 text-green-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M8.433 7.418c.155-.103.346-.196.567-.267v1.698a2.305 2.305 0 01-.567-.267C8.07 8.34 8 8.114 8 8c0-.114.07-.34.433-.582zM11 12.849v-1.698c.22.071.412.164.567.267.364.243.433.468.433.582 0 .114-.07.34-.433.582a2.305 2.305 0 01-.567.267z"></path>
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-13a1 1 0 10-2 0v.092a4.535 4.535 0 00-1.676.662C6.602 6.234 6 7.009 6 8c0 .99.602 1.765 1.324 2.246.48.32 1.054.545 1.676.662v1.941c-.391-.127-.68-.317-.843-.504a1 1 0 10-1.51 1.31c.562.649 1.413 1.076 2.353 1.253V15a1 1 0 102 0v-.092a4.535 4.535 0 001.676-.662C13.398 13.766 14 12.991 14 12c0-.99-.602-1.765-1.324-2.246A4.535 4.535 0 0011 9.092V7.151c.391.127.68.317.843.504a1 1 0 101.511-1.31c-.563-.649-1.413-1.076-2.354-1.253V5z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1">
                        <h4 class="text-lg font-bold text-green-900 mb-1">¡Tienes un torneo GRATIS disponible!</h4>
                        <p class="text-green-800 text-sm">
                            Tienes <strong>${{ number_format($creditoDisponible->monto, 0, ',', '.') }}</strong> de crédito por referidos.
                            Puedes usar este crédito para crear este torneo completamente gratis.
                        </p>
                        <p class="text-green-700 text-xs mt-2">
                            Válido hasta: {{ $creditoDisponible->fecha_vencimiento->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                <form action="{{ route('pagos.usar-credito', $torneo) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg transition duration-200 shadow-lg flex items-center justify-center gap-2"
                            onclick="return confirm('¿Deseas usar tu crédito de referido para este torneo? Esta acción no se puede deshacer.')">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Usar Crédito Gratis
                    </button>
                </form>
            </div>

            <div class="text-center mb-4">
                <span class="text-gray-500 text-sm">O si prefieres, paga con Mercado Pago:</span>
            </div>
        @endif

        <div class="bg-blue-50 border border-brand-200 rounded-lg p-4 mb-6">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-brand-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                </svg>
                <div class="text-sm text-blue-800">
                    <p class="font-semibold mb-1">Información importante:</p>
                    <ul class="list-disc list-inside space-y-1 text-blue-700">
                        <li>El pago se procesa de forma segura a través de Mercado Pago</li>
                        <li>Puedes pagar con tarjeta de crédito, débito o efectivo</li>
                        <li>Una vez confirmado el pago, podrás acceder al torneo inmediatamente</li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
            <a href="{{ $preference->init_point ?? $preference->sandbox_init_point }}"
               class="flex-1 bg-gradient-to-r from-brand-700 to-brand-600 text-white text-center px-6 py-3 rounded-lg font-semibold hover:from-brand-800 hover:to-brand-700 transition duration-200 shadow-lg flex items-center justify-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                </svg>
                Pagar con Mercado Pago
            </a>

            <a href="{{ route('torneos.index') }}"
               class="bg-gray-200 text-gray-700 text-center px-6 py-3 rounded-lg font-semibold hover:bg-gray-300 transition duration-200">
                Volver más tarde
            </a>
        </div>

        <form action="{{ route('torneos.destroy', $torneo) }}" method="POST" class="mt-4" onsubmit="return confirm('¿Estás seguro de que deseas eliminar este torneo?');">
            @csrf
            @method('DELETE')
            <button type="submit" class="w-full text-red-600 hover:text-red-700 text-sm font-medium">
                Cancelar y eliminar torneo
            </button>
        </form>
    </div>
</div>
@endsection
