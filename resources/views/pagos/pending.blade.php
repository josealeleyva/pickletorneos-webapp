@extends('layouts.dashboard')

@section('title', 'Pago Pendiente')
@section('page-title', 'Pago en Proceso')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-sm p-8 text-center">
        <div class="mb-6">
            <div class="inline-flex items-center justify-center w-20 h-20 bg-yellow-100 rounded-full mb-4">
                <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-3xl font-bold text-gray-900 mb-2">
                Pago en Proceso
            </h2>
            <p class="text-lg text-gray-600">
                Tu pago está siendo procesado
            </p>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <p class="text-sm text-yellow-800">
                Estamos esperando la confirmación del pago. Esto puede tardar unos minutos. Te notificaremos cuando el pago sea confirmado.
            </p>
        </div>

        <div class="flex flex-col gap-3">
            <a href="{{ route('torneos.index') }}"
               class="bg-indigo-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-indigo-700 transition duration-200">
                Volver a mis torneos
            </a>
            <a href="{{ route('pagos.checkout', $torneo) }}"
               class="text-indigo-600 hover:text-indigo-700 font-medium">
                Ver estado del pago
            </a>
        </div>
    </div>
</div>
@endsection
