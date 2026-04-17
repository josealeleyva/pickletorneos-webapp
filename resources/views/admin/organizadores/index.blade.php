@extends('admin.layouts.app')

@section('title', 'Organizadores')
@section('page-title', 'Gestión de Organizadores')

@section('content')
<div class="space-y-6">
    <!-- Filtros y Búsqueda -->
    <div class="bg-white rounded-lg shadow p-6">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                <input type="text" name="buscar" value="{{ request('buscar') }}" placeholder="Nombre, email..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                <select name="estado" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                    <option value="">Todos</option>
                    <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activos</option>
                    <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivos</option>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="w-full bg-brand-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition">
                    <i class="fas fa-search mr-2"></i>Buscar
                </button>
            </div>
        </form>
    </div>

    <!-- Tabla de Organizadores -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto -mx-4 md:mx-0">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Organizador</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Registro</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Torneos</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Total Pagado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Estado</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($organizadores as $org)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-10 h-10 rounded-full bg-blue-500 flex items-center justify-center text-white font-bold">
                                        {{ strtoupper(substr($org->nombre, 0, 1))}}{{ strtoupper(substr($org->apellido, 0, 1)) }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $org->nombre }} {{ $org->apellido }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $org->email }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $org->created_at->format('d/m/Y') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">{{ $org->torneos_count }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-bold text-green-600">${{ number_format($org->total_pagado, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                @if($org->cuenta_activa)
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Activo</span>
                                @else
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">Inactivo</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium space-x-2">
                                <a href="{{ route('admin.organizadores.show', $org) }}" class="text-brand-600 hover:text-blue-900" title="Ver detalle">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <form action="{{ route('admin.organizadores.toggle-estado', $org) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="{{ $org->cuenta_activa ? 'text-red-600 hover:text-red-900' : 'text-green-600 hover:text-green-900' }}" title="{{ $org->cuenta_activa ? 'Desactivar' : 'Activar' }}">
                                        <i class="fas fa-{{ $org->cuenta_activa ? 'ban' : 'check-circle' }}"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">No se encontraron organizadores</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $organizadores->links() }}
        </div>
    </div>
</div>
@endsection
