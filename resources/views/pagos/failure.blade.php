@extends('layouts.dashboard')

@section('title', 'Pago Fallido')
@section('page-title', 'Error en el Pago')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                No se pudo procesar el pago
            </h2>
            <p class="text-lg text-gray-600">
                Hubo un problema al procesar tu pago
            </p>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-red-800 mb-2">
                El pago no pudo ser completado. Esto puede deberse a:
            </p>
            <ul class="text-sm text-red-700 text-left list-disc list-inside space-y-1">
                <li>Fondos insuficientes</li>
                <li>Datos de tarjeta incorrectos</li>
                <li>Cancelación del pago</li>
                <li>Problemas con el método de pago</li>
            </ul>
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('pagos.checkout', $torneo) }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-200">
                Intentar nuevamente
            </a>
            <a href="{{ route('torneos.index') }}"
               class="text-gray-600 hover:text-gray-700 font-medium">
                Volver a mis torneos
            </a>
        </div>
    </div>
</div>
@endsection
