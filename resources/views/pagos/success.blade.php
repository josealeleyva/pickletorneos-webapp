@extends('layouts.dashboard')

@section('title', 'Pago Exitoso')
@section('page-title', 'Pago Confirmado')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-green-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                ¡Pago Confirmado!
            </h2>
            <p class="text-lg text-gray-600">
                Tu pago ha sido procesado exitosamente
            </p>
        </div>

        <div class="bg-gray-50 rounded-lg p-6 mb-6">
            <h3 class="font-semibold text-gray-900 mb-4">Detalles del pago</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Torneo:</span>
                    <span class="font-medium text-gray-900">{{ $torneo->nombre }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Monto pagado:</span>
                    <span class="font-medium text-gray-900">${{ number_format($pago->monto, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Estado:</span>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        Pagado
                    </span>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-blue-800">
                Ya puedes continuar con la configuración de tu torneo. Podrás agregar equipos, configurar grupos, generar el fixture y publicar el torneo.
            </p>
        </div>

        <a href="{{ route('torneos.show', $torneo) }}"
           class="inline-block bg-gradient-to-r from-brand-600 to-purple-600 text-white px-8 py-3 rounded-lg font-semibold hover:from-brand-700 hover:to-purple-700 transition duration-200 shadow-lg">
            Ir a mi torneo
        </a>
    </div>
</div>
@endsection
